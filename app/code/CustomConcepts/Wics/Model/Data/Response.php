<?php
declare(strict_types=1);

namespace CustomConcepts\Wics\Model\Data;

use CustomConcepts\Wics\Api\Data\WicsItemResponseInterface;

class Response implements WicsItemResponseInterface
{
    const SKU_FIELD_NAME = 'itemCode';
    const QTY_FIELD_NAME = 'nettoSalable';
    private $items = [];
    private $index = 0;

    /**
     * @inheritDoc
     */
    public function addItem(array $item) : void
    {
        $this->items[] = $item;
    }

    /**
     * @inheritDoc
     */
    public function getResponseHash(): string
    {
        $stringToHash =
            implode(array_column($this->items, self::SKU_FIELD_NAME)) .
            implode(array_column($this->items, self::QTY_FIELD_NAME));

        return $stringToHash ? sha1($stringToHash) : '';
    }

    /**
     * @inheritDoc
     */
    public function getSkuPool() : array
    {
        return array_column($this->items, self::SKU_FIELD_NAME);
    }

    /**
     * @inheritDoc
     */
    public function getQtyBySku(string $sku)
    {
        $key = array_search($sku, array_column($this->items, self::SKU_FIELD_NAME));
        if ($key !== false) {
            return (int)$this->items[$key][self::QTY_FIELD_NAME];
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function isResponseEmpty(): bool
    {
        return !(bool)$this->items;
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return $this->items[$this->index];
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        ++$this->index;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return isset($this->items[$this->index]);
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->index = 0;
    }
}
