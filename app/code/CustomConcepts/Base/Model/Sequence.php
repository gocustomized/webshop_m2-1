<?php
namespace CustomConcepts\Base\Model;

use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Framework\DB\Sequence\SequenceInterface;
use Magento\SalesSequence\Model\Meta;

class Sequence implements SequenceInterface
{
    /**
     * Default pattern for Sequence
     */
    const DEFAULT_PATTERN  = "%s%'.09d%s";

    /**
     * @var string
     */
    private $lastIncrementId;

    /**
     * @var Meta
     */
    private $meta;

    /**
     * @var false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var string
     */
    private $pattern;

    protected $resource;

    /**
     * @param Meta $meta
     * @param AppResource $resource
     * @param string $pattern
     */
    public function __construct(
        Meta $meta,
        AppResource $resource,
        $pattern = self::DEFAULT_PATTERN
    ) {
        $this->meta = $meta;
        $this->connection = $resource->getConnection('sales');
        $this->pattern = $pattern;
        $this->resource = $resource;
    }

    /**
     * Retrieve next value
     *
     * @return string
     */
    public function getNextValue()
    {
        $entityType = $this->meta->getEntityType();
        switch ($entityType){
            case 'order':
                $tableName = 'sales_order';
                break;
            case 'invoice':
                $tableName = 'sales_invoice';
                break;
            case 'shipment':
                $tableName = 'sales_shipment';
                break;
            case 'creditmemo':
                $tableName = 'sales_creditmemo';
                break;
            default:
                $tableName = false;
        }

        if ($tableName){
            return $this->getIncrementIds($tableName);
        } else {
            return $this->getNextValueOrig();
        }
    }

    public function getIncrementIds($tableName){
        $rnd = 0;
        $connection =  $this->resource->getConnection();
//        $sql = "SELECT temp.rnd FROM $tableName c,(SELECT FLOOR((RAND()*99999999)) AS rnd FROM $tableName LIMIT 0,50) AS temp
//    		 WHERE c.increment_id NOT IN (temp.rnd) LIMIT 1";
        $sql = "SELECT temp.rnd FROM (SELECT FLOOR((RAND()*99999999)) AS rnd FROM $tableName LIMIT 0,50) AS temp
                WHERE temp.rnd NOT IN (Select increment_id from $tableName) LIMIT 1";

        do{
            $result = $connection->fetchAll($sql);
        } while(empty($result));

        if(isset($result[0]['rnd'])){
            $rnd = $result[0]['rnd'];
        } else {
            $sql = "SELECT FLOOR((RAND()*99999999)) AS rnd LIMIT 1";
            $result = $connection->fetchAll($sql);
            $rnd = $result[0]['rnd'];
        }

        return $rnd;
    }

    /**
     * Retrieve current value
     *
     * @return string
     */
    public function getCurrentValue()
    {
        if (!isset($this->lastIncrementId)) {
            return null;
        }

        return sprintf(
            $this->pattern,
            $this->meta->getActiveProfile()->getPrefix(),
            $this->calculateCurrentValue(),
            $this->meta->getActiveProfile()->getSuffix()
        );
    }

    /**
     * Retrieve next value
     *
     * @return string
     */
    public function getNextValueOrig()
    {
        $this->connection->insert($this->meta->getSequenceTable(), []);
        $this->lastIncrementId = $this->connection->lastInsertId($this->meta->getSequenceTable());
        return $this->getCurrentValue();
    }

    /**
     * Calculate current value depends on start value
     *
     * @return string
     */
    private function calculateCurrentValue()
    {
        return ($this->lastIncrementId - $this->meta->getActiveProfile()->getStartValue())
            * $this->meta->getActiveProfile()->getStep() + $this->meta->getActiveProfile()->getStartValue();
    }
}
