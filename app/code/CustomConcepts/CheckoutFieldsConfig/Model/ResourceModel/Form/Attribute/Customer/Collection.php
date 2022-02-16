<?php
namespace CustomConcepts\CheckoutFieldsConfig\Model\ResourceModel\Form\Attribute\Customer;

/**
 * Code from Magento\Customer\Model\ResourceModel\Form\Attribute\Collection
 */

class Collection extends \CustomConcepts\CheckoutFieldsConfig\Model\ResourceModel\Form\Attribute\Eav\Collection
{
    /**
     * Current module pathname
     *
     * @var string
     */
    protected $_moduleName = 'Magento_Customer';

    /**
     * Current EAV entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = 'customer';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Magento\Eav\Model\Attribute::class, \Magento\Customer\Model\ResourceModel\Form\Attribute::class);
    }

    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored.
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    protected function _getEavWebsiteTable()
    {
        return $this->getTable('customer_eav_attribute_website');
    }
}
