<?php
declare(strict_types=1);

namespace CustomConcepts\Wics\Model;

class Sync extends \Magento\Framework\Model\AbstractExtensibleModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\CustomConcepts\Wics\Model\ResourceModel\Sync::class);
    }

    /**
     * Get response hash string
     *
     * @return string
     */
    public function getResponseHash()
    {
        return $this->getData('response_hash');
    }

    /**
     * Set response hash string
     *
     * @param string $value
     */
    public function setResponseHash(string $value)
    {
        $this->setData('response_hash', $value);
    }

    /**
     * Get sync status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * Set sync status
     *
     * @param string $value
     */
    public function setStatus(string $value)
    {
        $this->setData('status', $value);
    }

    /**
     * Get sync message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->getData('message');
    }

    /**
     * Set sync message
     *
     * @param string $value
     */
    public function setMessage(string $value)
    {
        $this->setData('message', $value);
    }

    /**
     * Get sync creation date
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData('creation_time');
    }

    /**
     * Set sync creation date
     *
     * @param string $value
     */
    public function setCreatedAt(string $value)
    {
        $this->setData('creation_time', $value);
    }
}
