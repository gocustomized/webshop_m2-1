<?php
namespace CustomConcepts\Estimations\ViewModel;

class EstimationsViewModel implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \CustomConcepts\Estimations\Helper\Data
     */
    protected $estimationsHelper;

    public function __construct(
        \CustomConcepts\Estimations\Helper\Data $estimationsHelper
    ){
        $this->estimationsHelper = $estimationsHelper;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getEdd(){
        return $this->estimationsHelper->getEdd();
    }

    /**
     * @param $date
     * @return false|string
     */
    public function formatDate($date){
        return date('l, F j, Y', strtotime($date));
    }
}
