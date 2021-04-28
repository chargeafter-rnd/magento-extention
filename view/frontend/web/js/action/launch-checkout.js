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
], function (quote, customer, launchCheckoutService) {
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

        shippingAddress.street.forEach((line,index)=>consumerDetails.shippingAddress[`line${++index}`]=line);
        billingAddress.street.forEach((line,index)=>consumerDetails.billingAddress[`line${++index}`]=line);

        if(customer.isLoggedIn()){
            consumerDetails.email=customer.customerData.email;
            consumerDetails.merchantConsumerId=customer.customerData.id;
        }else{
            consumerDetails.email=quote.guestEmail;
        }

        const totals = quote.totals();

        const cartDetails = {
            taxAmount: totals.tax_amount,
            shippingAmount: totals.shipping_amount,
            totalAmount: totals.base_grand_total,
        };

        if(totals.discount_amount){
            cartDetails.discounts = [
                {
                    name: 'DISCOUNT',
                    amount: Math.abs(totals.discount_amount),
                }
            ];
        }

        cartDetails.items = quote.getItems().map(item=>({
            name: item.name,
            price: parseFloat(item.price),
            sku: item.sku,
            quantity: item.qty,
            //leasable: true,
            //productCategory: "Product category",
            /*warranty: {
                name: "Awesome Warranty",
                price: 100.0,
                sku: "AWSMWRNTY"
            }*/
        }));

        const options =  {
            consumerDetails,

            cartDetails,

            onDataUpdate(updatedData, callback) {
                callback();
            },

        };

        return launchCheckoutService(options, messageContainer);
    };
});
