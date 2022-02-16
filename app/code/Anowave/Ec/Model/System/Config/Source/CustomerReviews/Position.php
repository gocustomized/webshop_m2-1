<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec\Model\System\Config\Source\CustomerReviews;

class Position implements \Magento\Framework\Option\ArrayInterface
{
    const BOTTOM_RIGHT  = 'BOTTOM_RIGHT';
    const BOTTOM_LEFT   = 'BOTTOM_LEFT';
    const INLINE        = 'INLINE';
	
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		return 
		[
			[
				'value' => static::BOTTOM_RIGHT, 
				'label' => __('Bottom Right')
			],
			[
				'value' => static::BOTTOM_LEFT, 
				'label' => __('Bottom left')
			],
		    [
		        'value' => static::INLINE,
		        'label' => __('Inline')
		    ]
		];
	}
}