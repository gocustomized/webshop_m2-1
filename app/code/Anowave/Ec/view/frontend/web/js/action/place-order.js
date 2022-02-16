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

define(['jquery','mage/utils/wrapper'], function ($, wrapper) 
{
    'use strict';

    return function (placeOrderAction) 
    {
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) 
        {
        	if ('undefined' !== typeof data && 'undefined' !== typeof AEC.Const.CHECKOUT_STEP_ORDER)
        	{
        		/**
        		 * Set step
        		 */
        		data.ecommerce.checkout.actionField.step = AEC.Const.CHECKOUT_STEP_ORDER;
        		
        		if (AEC.Const.COOKIE_DIRECTIVE)
				{
					if (AEC.Const.COOKIE_DIRECTIVE_CONSENT_GRANTED)
					{
		        		dataLayer.push(data);
					}
				}
        		else 
        		{
        			dataLayer.push(data);
        		}
        	}

            return originalAction(paymentData, messageContainer);
        });
    };
});