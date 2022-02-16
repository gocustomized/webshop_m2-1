<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CustomConcepts\Base\Plugin\Model;

class Sequence
{
    protected $connection;

    public function __construct(\Magento\Framework\App\ResourceConnection $connection) {
        $this->connection = $connection;
    }

    public function aroundGetNextValue(\Magento\SalesSequence\Model\Sequence $subject, callable $proceed)
    {
        return $this->getIncrementIds();

    }

    public function getIncrementIds(){
        $rnd = 0;
        $connection =  $this->connection->getConnection();
        $sql = "SELECT temp.rnd FROM sales_order c,(SELECT FLOOR((RAND()*99999999)) AS rnd FROM sales_order LIMIT 0,50) AS temp
    		 WHERE c.increment_id NOT IN (temp.rnd) LIMIT 1";
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

}
?>
