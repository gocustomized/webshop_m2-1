<?php


/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Score
 */
class Score extends Column {
    /**
     *
     * @var type \CustomConcepts\Kiyoh\Model\Stats
     */
    protected $stats;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CollectionFactory $collectionFactory
     * @param \CustomConcepts\Kiyoh\Model\Stats $stats
     * @param array $components
     * @param array $data
     */
    public function __construct(
    ContextInterface $context, UiComponentFactory $uiComponentFactory, \CustomConcepts\Kiyoh\Model\Stats $stats, array $components = [], array $data = []
    ) {
        $this->stats = $stats;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {

                $value = $item['score'];

                if ($value == '0') {
                    $output = '';
                } else {
                    $o = 0;
                    $output = '<span class="rating-empty"><span class="rating-star-' . $value . '"></span></span>';
                    $output .= '<div class="tooltip">'
                            . '<span class="help"><span></span></span>'
                            . '<div class="tooltip-content">';

                    $shop_id = $item['shop_id'];
                    $review_stats =$this->stats->load($shop_id, 'shop_id');

                    $output .= '<strong>' . __('Overall') . ':</strong> ' . $item['score'] . '/10<br>';

                    if ($item['score_q2'] > 0) {
                        $output .= '<strong>' . $review_stats->getScoreQ2Title() . ':</strong> ' . $item['score_q2'] . '/10<br>';
                        $o++;
                    }
                    if ($item['score_q3'] > 0) {
                        $output .= '<strong>' . $review_stats->getScoreQ3Title() . '</strong> ' . $item['score_q3'] . '/10<br>';
                        $o++;
                    }
                    if ($item['score_q4'] > 0) {
                        $output .= '<strong>' . $review_stats->getScoreQ4Title() . '</strong> ' . $item['score_q4'] . '/10<br>';
                        $o++;
                    }
                    if ($item['score_q5'] > 0) {
                        $output .= '<strong>' . $review_stats->getScoreQ5Title() . '</strong> ' . $item['score_q5'] . '/10<br>';
                        $o++;
                    }
                    if ($item['score_q6'] > 0) {
                        $output .= '<strong>' . $review_stats->getScoreQ6Title() . '</strong> ' . $item['score_q6'] . '/10<br>';
                        $o++;
                    }
                    if ($item['score_q7'] > 0) {
                        $output .= '<strong>' . $review_stats->getScoreQ7Title() . '</strong> ' . $item['score_q7'] . '/10<br>';
                        $o++;
                    }
                    if ($item['score_q8'] > 0) {
                        $output .= '<strong>' . $review_stats->getScoreQ8Title() . '</strong> ' . $item['score_q8'] . '/10<br>';
                        $o++;
                    }
                    if ($item['score_q9'] > 0) {
                        $output .= '<strong>' . $review_stats->getScoreQ9Title() . '</strong> ' . $item['score_q9'] . '/10<br>';
                        $o++;
                    }
                    if ($item['score_q10'] > 0) {
                        $output .= '<strong>' . $review_stats->getScoreQ10Title() . '</strong> ' . $item['score_q10'] . '/10<br>';
                        $o++;
                    }

                    if ($o > 0) {
                        $output .= '<br/>';
                    }

                    if ($item['positive']) {
                        $output .= '<strong>' . __('Positive') . ':</strong> ' . $item['positive'] . '<br>';
                    }
                    if ($item['negative']) {
                        $output .= '<strong>' . __('Negative') . ':</strong> ' . $item['negative'] . '<br>';
                    }
                    if ($item['reaction']) {
                        $output .= '<strong>' . __('Reaction') . ':</strong> ' . $item['reaction'] . '<br>';
                    }

                    $output .= '</span></div></div>';
                }


                $item[$this->getData('name')] = $output;
            }
        }

        return $dataSource;
    }

}
