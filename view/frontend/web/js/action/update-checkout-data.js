/*
 * ChargeAfter
 *
 * @category    Payment Gateway
 * @package     Chargeafter_Payment
 * @copyright   Copyright (c) 2021 ChargeAfter.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      taras@lagan.com.ua
 */

define([
    'jquery',
    'Chargeafter_Payment/js/model/payment-config',
    'Magento_Checkout/js/model/quote',
    'Chargeafter_Payment/js/model/error-processor',
    'Magento_Checkout/js/action/get-totals',
    'Chargeafter_Payment/js/action/update-customer-data',
], function (
    $,
    paymentConfig,
    quote,
    errorProcessor,
    updateTotalsAction,
    updateCustomerData
) {
    'use strict';

    const shouldUpdateConsumerData = paymentConfig.getConfig('shouldUpdateConsumerData');

    const resolveTotals = function (deferred) {
        const defer = $.Deferred();
        const updatedTotals = defer.pipe();

        updateTotalsAction([], defer);

        updatedTotals.always(function () {
            const totals = quote.totals();

            deferred.resolve({
                taxAmount: totals.tax_amount,
                shippingAmount: totals.shipping_amount,
                totalAmount: totals.base_grand_total,
            });
        });
    }

    return function (options) {
        return $.Deferred(function (deferred) {
            if (!shouldUpdateConsumerData) {
                deferred.resolve({});
            } else {
                updateCustomerData(options);
                resolveTotals(deferred);
            }
        });
    }
});
