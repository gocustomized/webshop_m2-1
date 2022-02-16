<?php
namespace CustomConcepts\Estimations\Block\Adminhtml\Deliveryrate\Edit\Tab;
class NewDeliveryRate extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    private $countryFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        $this->countryFactory = $countryFactory;
        parent::__construct($context, $registry, $formFactory, $data);

    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
		/* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('estimations_deliveryrate');
		$isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('New Delivery Rate')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }
        $countryOptions = $this->countryFactory->toOptionArray();
        $fieldset->addField(
            'country_id',
            'select',
            [
                'name' => 'country_id',
                'label' => __('country'),
                'title' => __('country'),
                'values' => $countryOptions
                /*'required' => true,*/
            ]
        );
		$fieldset->addField(
            'carrier_id',
            'text',
            array(
                'name' => 'carrier_id',
                'label' => __('carrier'),
                'title' => __('carrier'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'leadtime_min',
            'text',
            array(
                'name' => 'leadtime_min',
                'label' => __('leadtime min'),
                'title' => __('leadtime min'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'leadtime_max',
            'text',
            array(
                'name' => 'leadtime_max',
                'label' => __('leadtime max'),
                'title' => __('leadtime max'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'week_schedule',
            'text',
            array(
                'name' => 'week_schedule',
                'label' => __('weekend'),
                'title' => __('weekend'),
                /*'required' => true,*/
            )
        );
		/*{{CedAddFormField}}*/

        if (!$model->getId()) {
            $model->setData('status', $isElementDisabled ? '2' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('New Delivery Rate');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('New Delivery Rate');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
