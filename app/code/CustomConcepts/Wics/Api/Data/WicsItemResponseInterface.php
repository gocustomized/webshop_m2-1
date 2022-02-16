<?php
declare(strict_types=1);

namespace CustomConcepts\Wics\Api\Data;

interface WicsItemResponseInterface extends \Iterator
{
    /**
     * @param array $item
     */
    public function addItem(array $item) : void;

    /**
     * @param string $sku
     * @return int | bool
     */
    public function getQtyBySku(string $sku);

    /**
     * @return array
     */
    public function getSkuPool() : array;

    /**
     * @return string
     */
    public function getResponseHash() : string;

    /**
     * @return bool
     */
    public function isResponseEmpty() : bool;
}
