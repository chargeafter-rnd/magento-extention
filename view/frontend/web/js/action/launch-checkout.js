/*
 * ChargeAfter
 *
 * @category    Payment Gateway
 * @package     Chargeafter_Payment
 * @copyright   Copyright (c) 2021 ChargeAfter.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      taras@lagan.com.ua
 */

/**
 * @api
 */
define([
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Chargeafter_Payment/js/model/launch-checkout',
    'Chargeafter_Payment/js/action/update-checkout-data'
], function (
    quote,
    customer,
    launchCheckoutService,
    updateCheckoutDataAction
) {
    'use strict';

    return function (messageContainer) {

        const billingAddress = quote.billingAddress();
        const shippingAddress = quote.shippingAddress();

        const consumerDetails = {
            firstName: billingAddress.firstname,
            lastName: billingAddress.lastname,
            mobilePhoneNumber: billingAddress.telephone,
            shippingAddress: {
                city: shippingAddress.city,
                zipCode: shippingAddress.postcode,
                state: shippingAddress.regionCode
            },
            billingAddress: {
                city: billingAddress.city,
                zipCode: billingAddress.postcode,
                state: billingAddress.regionCode
            }
        };

        shippingAddress.street.forEach((line, index) => consumerDetails.shippingAddress[`line${++index}`] = line);
        billingAddress.street.forEach((line, index) => consumerDetails.billingAddress[`line${++index}`] = line);

        if (customer.isLoggedIn()) {
            consumerDetails.email = customer.customerData.email;
            consumerDetails.merchantConsumerId = customer.customerData.id;
        } else {
            consumerDetails.email = quote.guestEmail;
        }

        const totals = quote.totals();

        const cartDetails = {
            taxAmount: totals.tax_amount,
            shippingAmount: totals.shipping_amount,
            totalAmount: totals.base_grand_total,
        };

        if (totals.discount_amount) {
            cartDetails.discounts = [
                {
                    name: 'DISCOUNT',
                    amount: Math.abs(totals.discount_amount),
                }
            ];
        }

        cartDetails.items = quote.getItems().map(item => {
            var chargeAfterOptions = 'chargeafter' in item ? item.chargeafter : {};

            var lineItem = {
                name: item.name,
                price: parseFloat(item.price),
                sku: item.sku,
                quantity: item.qty,
                leasable: 'leasable' in chargeAfterOptions ? chargeAfterOptions['leasable'] : true
            }

            if ('warranty' in chargeAfterOptions && chargeAfterOptions['warranty']) {
                lineItem.warranty = {
                    name: item.name,
                    price: 0,
                    sku: item.sku,
                }
            }

            return lineItem;
        });

        const preferences = {
            language: 'en',
            currency: totals.quote_currency_code || totals.base_currency_code,
            country: billingAddress.country_id || 'US',
        };

        const options = {
            consumerDetails,

            cartDetails,

            onDataUpdate(updatedData, callback) {
                return updateCheckoutDataAction(updatedData);
            },

            preferences
        };

        return launchCheckoutService(options, messageContainer);
    };
});
