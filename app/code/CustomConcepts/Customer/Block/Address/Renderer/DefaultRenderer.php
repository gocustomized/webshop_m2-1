<?php

namespace CustomConcepts\Customer\Block\Address\Renderer;

use Magento\Customer\Model\Address\AddressModelInterface;
use Magento\Customer\Model\Address\Mapper;
use Magento\Customer\Model\Metadata\ElementFactory;

/**
 * Address format renderer default
 */
class DefaultRenderer extends \Magento\Customer\Block\Address\Renderer\DefaultRenderer
{


    
    /**
     * Render address by attribute array
     *
     * @param array $addressAttributes
     * @param Format|null $format
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function renderArray($addressAttributes, $format = null)
    {
        switch ($this->getType()->getCode()) {
            case 'html':
                $dataFormat = ElementFactory::OUTPUT_FORMAT_HTML;
                break;
            case 'pdf':
                $dataFormat = ElementFactory::OUTPUT_FORMAT_PDF;
                break;
            case 'oneline':
                $dataFormat = ElementFactory::OUTPUT_FORMAT_ONELINE;
                break;
            default:
                $dataFormat = ElementFactory::OUTPUT_FORMAT_TEXT;
                break;
        }

        $attributesMetadata = $this->_addressMetadataService->getAllAttributesMetadata();
        $data = [];
        foreach ($attributesMetadata as $attributeMetadata) {
            if (!$attributeMetadata->isVisible()) {
                continue;
            }
            $attributeCode = $attributeMetadata->getAttributeCode();
            if ($attributeCode == 'country_id' && isset($addressAttributes['country_id'])) {
                // Added $addressAttributes['country_id'] param on getName to fetch translated country name based on locale
                // Use Dutch locale for BE country
                $countryId = $addressAttributes['country_id']; // We use a separate variable so we can keep the original country id
                if ($countryId == "BE") {
                    $countryId = "NL";
                }
                $data['country'] = $this->_countryFactory->create()->loadByCode(
                    $addressAttributes['country_id']
                )->getName($countryId);
                // end
            } elseif ($attributeCode == 'region' && isset($addressAttributes['region'])) {
                $data['region'] = (string)__($addressAttributes['region']);
            } elseif (isset($addressAttributes[$attributeCode])) {
                $value = $addressAttributes[$attributeCode];
                $dataModel = $this->_elementFactory->create($attributeMetadata, $value, 'customer_address');
                $value = $dataModel->outputValue($dataFormat);
                if ($attributeMetadata->getFrontendInput() == 'multiline') {
                    $values = $dataModel->outputValue(ElementFactory::OUTPUT_FORMAT_ARRAY);
                    // explode lines
                    foreach ($values as $k => $v) {
                        $key = sprintf('%s%d', $attributeCode, $k + 1);
                        $data[$key] = $v;
                    }
                }
                $data[$attributeCode] = $value;
            }
        }
        if ($this->getType()->getEscapeHtml()) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->escapeHtml($value);
            }
        }
        $format = $format !== null ? $format : $this->getFormatArray($addressAttributes);
        return $this->filterManager->template($format, ['variables' => $data]);
    }
}
