<?php
namespace CustomConcepts\TranssmartSmartConnect\Model\Config;


class StructurePlugin
{
    /**
     * @var \CustomConcepts\TranssmartSmartConnect\Helper\Data
     */
    protected $tscHelper;

    /**
     * Structure constructor.
     * @param \CustomConcepts\TranssmartSmartConnect\Helper\Data $tscHelper
     */
    public function __construct(
        \CustomConcepts\TranssmartSmartConnect\Helper\Data $tscHelper
    ){
        $this->tscHelper = $tscHelper;
    }

    /**
     * @param \Magento\Config\Model\Config\Structure $subject
     * @param $result
     * @return array
     */
    public function afterGetFieldPaths(\Magento\Config\Model\Config\Structure $subject, $result){
        $systemXmlMapping = $this->tscHelper->getSystemXmlMapping();
        if($systemXmlMapping){
            $result += $systemXmlMapping;
        }

        return $result;
    }

}
