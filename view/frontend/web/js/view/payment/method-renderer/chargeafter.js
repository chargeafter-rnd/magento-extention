/*
 * ChargeAfter
 *
 * @category    Payment Gateway
 * @package     Chargeafter_Payment
 * @copyright   Copyright (c) 2021 ChargeAfter.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      taras@lagan.com.ua
 */

define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Chargeafter_Payment/js/model/load-api',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Chargeafter_Payment/js/action/launch-checkout',
        'Magento_Checkout/js/action/place-order'
    ],
    function ($, Component, loadApi, quote, additionalValidators, launchCheckoutAction, placeOrderAction) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Chargeafter_Payment/payment/chargeafter',
                responseData: null,
            },
            initialize: function () {
                this._super();
                loadApi(this.getConfig('cdnUrl'), {
                    apiKey: this.getConfig('publicKey'),
                    // Optional:
                    // storeId: 'your-store-id'
                });
                return this;
            },
            getConfig: function (key){
                return window.checkoutConfig.payment[this.item.method][key];
            },
            /**
             * @return {*}
             */
            getPlaceOrderDeferredObject: function () {
                return launchCheckoutAction(this.messageContainer).then(result=>{
                    const data = this.getData();
                    data.additional_data = {
                      token: result.token,
                      data: JSON.stringify(result.data),
                    };
                    return $.when(
                      placeOrderAction(data, this.messageContainer)
                    );
                });
            },
        });
    }
);
