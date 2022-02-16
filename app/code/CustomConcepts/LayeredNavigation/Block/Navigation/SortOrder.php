<?php
namespace CustomConcepts\LayeredNavigation\Block\Navigation;

use Magento\Framework\View\Element\Template;

class SortOrder extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $catalogConfig;

    protected $currentSorting;
    protected $currentOrder;

    protected $categoryInterface;

    /**
     * SortOrder constructor.
     * @param Template\Context $context
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Catalog\Api\Data\CategoryInterface $categoryInterface,
        array $data = []
    ){
        $this->catalogConfig = $catalogConfig;
        $this->categoryInterface = $categoryInterface;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';
//        $availableOrders = $this->catalogConfig->getAttributeUsedForSortByArray();
        $availableOrders = $this->categoryInterface->getAvailableSortByOptions();
        $this->currentSorting = $this->getRequest()->getParam('product_list_order');
        $this->currentOrder = $this->getRequest()->getParam('product_list_dir');

        $html .= '<div id="sortlist" class="mobile-show">';

            $html .= '<div id="sortcloser"><span>' . __('Sort By') . '</span></div>';

            $html .= '<div class="scrollouter">';
                $html .= '<div class="scrollinner">';
                    $html .= '<ul>';
                        foreach($availableOrders as $key => $value){
                            $html .= $this->generateSortListItem($key, $value, 'asc');
                            $html .= $this->generateSortListItem($key, $value, 'desc');
                        }
                    $html .= '</ul>';
                    $html .= '<button id="sortbutton" data-mage-init=\'{"CustomConcepts_LayeredNavigation/js/sortbutton": {}}\'>sort</button>';
                $html .= '</div>';
            $html .= '</div>';

        $html .= '</div>';

        return $html;
    }

    /**
     * @param $key
     * @param $value
     * @param string $sortType
     * @return string
     */
    protected function generateSortListItem($key, $value, $sortType = 'asc'){
        $urlParams = ['product_list_order' => $key];
        if($sortType == 'desc'){
            $sortLabel = __('Set Descending Direction');
            $urlParams['product_list_dir'] = 'desc';
        } else { //asc
            $sortLabel = __('Set Ascending Direction');
            $urlParams['product_list_dir'] = 'asc';
        }

        $itemUrl = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $urlParams]);
        $checked = ($this->currentSorting == $key) && ($this->currentOrder == $sortType) ? 'checked="checked"' : '';

        if (!$this->currentSorting && !$this->currentOrder) {
            // Fetch current sort order and direction from toolbar block
            $toolbarBlock = $this->getLayout()->createBlock('Magento\Catalog\Block\Product\ProductList\Toolbar');
            $checked = ($toolbarBlock->getCurrentOrder() == $key) && ($toolbarBlock->getCurrentDirection() == $sortType) ? 'checked="checked"' : '';
        }
        
        $li = '<li>';
            $li .= '<label title="' . $sortLabel . '">';
                $li .= '<input name="sortoption"
                                type="radio"
                                value="' . $itemUrl . '"
                                ' . $checked . '>';
                $li .= __($value) . ' - ' . $sortLabel;
                $li .= '<span class="checkmark"></span>';
            $li .= '</label>';
        $li .= '</li>';

        return $li;
    }
}
