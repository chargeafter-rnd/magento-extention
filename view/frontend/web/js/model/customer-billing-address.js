/*
 * ChargeAfter
 *
 * @category    Payment Gateway
 * @package     Chargeafter_Payment
 * @copyright   Copyright (c) 2021 ChargeAfter.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      rnd-ua-team@chargeafter.com
 */

define([], function () {
    'use strict';

    var lastCustomerBillingAddress = null,
        isUpdatedCustomerBillingAddress = false;

    return {
        isUpdated: function () {
            return isUpdatedCustomerBillingAddress;
        },

        setUpdate: function (flag) {
            isUpdatedCustomerBillingAddress = flag;
        },

        hasLastCustomerBillingAddress: function () {
            return lastCustomerBillingAddress !== null;
        },

        setLastCustomerBillingAddress: function (address) {
            lastCustomerBillingAddress = address;
        },

        getLastCustomerBillingAddress: function () {
            return lastCustomerBillingAddress;
        }
    };
});
