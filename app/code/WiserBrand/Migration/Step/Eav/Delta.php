<?php

namespace WiserBrand\Migration\Step\Eav;

use Migration\App\Step\AbstractDelta;
use Migration\ResourceModel\Source;
use Migration\ResourceModel;


/**
 * Class Delta
 * @SuppressWarnings(CouplingBetweenObjects)
 */
class Delta extends AbstractDelta
{

    /**
     * @param Source $source
     * @param ResourceModel\Destination $destination
     */
    public function __construct(
        Source $source,
        ResourceModel\Destination $destination
    ) {
        $this->source = $source;
        $this->destination = $destination;
    }


    public function perform()
    {
        $attributes = [
            [
                "id" => 264,
                "type" => "int",
                'm1_id' => false
            ],
            [
                "id" => 262,
                "type" => "varchar",
                'm1_id' => false
            ],
            [
                "id" => 316,
                "type" => "datetime",
                'm1_id' => 70
            ]
        ];

        $connection_m2 = $this->destination->getAdapter();
        $connection_m1 = $this->source->getAdapter();


        foreach ($attributes as $attr) {
            $attr_id = $attr['id'];
            $attr_type = $attr['type'];
            $m1_attr_id = $attr['m1_id'];

            # 1. Get attributes that have already exists in M2
            $result = $connection_m2->loadDataFromSelect(
                "select * from catalog_product_entity_$attr_type as cpei
                    left join catalog_product_entity as cpe on cpe.entity_id = cpei.entity_id
                        where attribute_id = $attr_id"
            );

            $imported_ids = [];
            foreach ($result as $row) {
                if (isset($row['entity_id'])) {
                    $imported_ids[] = $row['entity_id'];
                }
            }

            # 2. Looking for missings in M1
            $not_in_condition = count($imported_ids) > 0 ?
                "NOT IN (" . implode(",", $imported_ids) . ")" : null;

            if($m1_attr_id){
                $result = $connection_m1->loadDataFromSelect(
                    "select cpei.* from mage_catalog_product_entity_$attr_type as cpei
                        left join mage_catalog_product_entity as cpe on cpe.entity_id = cpei.entity_id
                            where attribute_id = $m1_attr_id
                            and cpei.entity_id $not_in_condition
                    "
                );
            }else{
                $result = $connection_m1->loadDataFromSelect(
                    "select cpei.* from mage_catalog_product_entity_$attr_type as cpei
                        left join mage_catalog_product_entity as cpe on cpe.entity_id = cpei.entity_id
                            where attribute_id = $attr_id
                            and cpei.entity_id $not_in_condition
                    "
                );
            }
            echo "\nAttribute Id " . $attr_id . ": Missing Product Ids ---> " . count($result);


            # 3. Update M2 with missings values
            $rows = [];
            foreach($result as $row) {
                $rows[] = [
		    "attribute_id" => $m1_attr_id? $attr_id :$row['attribute_id'],
                    "store_id" => $row['store_id'],
                    "entity_id" => $row['entity_id'],
                    "value" => $row['value']
                ];
            }

            if (count($rows) > 0) {
                $connection_m2->insertRecords(
                    "catalog_product_entity_$attr_type",
                    $rows
                );
            }

        }

        return true;
    }
}

