<?php
namespace CustomConcepts\Base\Helper;

use Magento\Framework\App\Helper\Context;
use \Zend\Log\Logger as ZendLogger;
use Zend\Log\Writer\Stream;

class Logger extends \Magento\Framework\App\Helper\AbstractHelper {


    const DEFAULT_FILE = 'customconcepts.log';
    /**
     * @var string
     */
    protected $file;
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $dir;

    /**
     * Logger constructor.
     * @param Context $context
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Filesystem\DirectoryList $dir
    ){
        $this->dir = $dir;
        $this->file = self::DEFAULT_FILE;
        parent::__construct($context);
    }

    /**
     * @param $message
     * @param array $context
     * @param null $file
     * @param int $type
     */
    public function log($message, $context = [], $file = null, $type = 6){
        if(!$file){
            $file = $this->file;
        }
        $writer = new Stream($this->dir->getRoot() . '/var/log/' . $file);
        $logger = new ZendLogger();
        $logger->addWriter($writer);

//        EMERG   = 0;  // Emergency: system is unusable
//        ALERT   = 1;  // Alert: action must be taken immediately
//        CRIT    = 2;  // Critical: critical conditions
//        ERR     = 3;  // Error: error conditions
//        WARN    = 4;  // Warning: warning conditions
//        NOTICE  = 5;  // Notice: normal but significant condition
//        INFO    = 6;  // Informational: informational messages
//        DEBUG   = 7;  // Debug: debug messages
        $logger->log($type, $message);
        if($context){
            $logger->log($type, $context);
        }
    }

    /**
     * @param $message
     * @param array $context
     * @param null $file
     */
    public function info($message, $context = [], $file = null){
        $this->log($message, $context, $file, ZendLogger::INFO);
    }

    /**
     * @param $message
     * @param array $context
     * @param null $file
     */
    public function error($message, $context = [], $file = null){
        $this->log($message, $context, $file, ZendLogger::ERR);
    }

    /**
     * @param $message
     * @param array $context
     * @param null $file
     */
    public function warning($message, $context = [], $file = null){
        $this->log($message, $context, $file, ZendLogger::WARN);
    }

    /**
     * @param $file
     */
    public function setLogFile($file){
        $this->file = $file;
    }
}
