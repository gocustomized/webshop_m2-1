<?php
namespace CustomConcepts\Faqs\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class TopicThumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ){
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $filename = $item['image'];
                $item[$fieldName . '_src'] = $filename;
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'faqs/manage/edit',
                    ['topic_id' => $item['topic_id']]);
                $item[$fieldName . '_orig_src'] = $filename;
            }
        }

        return $dataSource;
    }
}
