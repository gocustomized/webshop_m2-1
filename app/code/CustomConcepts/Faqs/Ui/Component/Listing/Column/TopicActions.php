<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace CustomConcepts\Faqs\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Escaper;

/**
 * Class GroupActions
 */
class TopicActions extends Column
{
    /**
     * Url path
     */
    const URL_PATH_EDIT = 'faqs/manage/edit';
    const URL_PATH_DELETE = 'faqs/manage/delete';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['topic_id'])) {
                    $title = $this->escaper->escapeHtml($item['title']);
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'topic_id' => $item['topic_id']
                                ]
                            ),
                            'label' => __('Edit'),
                            '__disableTmpl' => true
                        ],
                    ];

                    $item[$this->getData('name')]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            static::URL_PATH_DELETE,
                            [
                                'topic_id' => $item['topic_id']
                            ]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete %1', $this->escaper->escapeHtml($title)),
                            'message' => __(
                                'Are you sure you want to delete %1 record?',
                                $this->escaper->escapeHtml($title)
                            )
                        ],
                        'post' => true,
                        '__disableTmpl' => true
                    ];
                }
            }
        }

        return $dataSource;
    }
}
