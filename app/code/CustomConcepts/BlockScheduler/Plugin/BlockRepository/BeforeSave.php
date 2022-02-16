<?php
namespace CustomConcepts\BlockScheduler\Plugin\BlockRepository;

use Magento\Framework\Exception\LocalizedException;

class BeforeSave
{
    protected $dateFilter;
    protected $dateTimeFilter;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateTimeFilter
    ){
        $this->dateFilter = $dateFilter;
        $this->dateTimeFilter = $dateTimeFilter;
    }

    public function beforeSave(\Magento\Cms\Api\BlockRepositoryInterface $subject, $block){
        $filterValues = [];
        if($block->getStartDate()){
            $filterValues['start_date'] = $this->dateTimeFilter;
            $block->setStartDate(date('d/m/Y H:i:s', strtotime($block->getStartDate())));
        }
        if($block->getEndDate()){
            $filterValues['end_date'] = $this->dateTimeFilter;
            $block->setEndDate(date('d/m/Y H:i:s', strtotime($block->getEndDate())));
        }
        $inputFilter = new \Zend_Filter_Input(
            $filterValues,
            [],
            $block->getData()
        );
        $data = $inputFilter->getUnescaped();
        $startDate = isset($data['start_date']) && !empty($data['start_date']) ? $data['start_date'] : null;
        $endDate = isset($data['end_date']) && !empty($data['end_date']) ? $data['end_date'] : null;

        $block->setStartDate($startDate);
        $block->setEndDate($endDate);

        $this->validateData($block);

        return [$block];
    }

    protected function validateData($block){
        if($block->hasStartDate() && $block->hasEndDate()){
            $startDate = new \DateTime($block->getStartDate());
            $endDate = new \DateTime($block->getEndDate());

            if ($startDate > $endDate) {
                throw new LocalizedException(__('Disable on date must follow Enable on date.'));
            }
        }
    }
}
