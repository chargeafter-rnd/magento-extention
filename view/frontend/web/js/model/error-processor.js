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
    'Magento_Ui/js/model/messageList',
    'mage/translate'
], function (globalMessageList, $t) {
    'use strict';

    return {
        /**
         * @param {Object} response
         * @param {Object} messageContainer
         */
        process: function (error, messageContainer) {

            messageContainer = messageContainer || globalMessageList;

            switch (error.code){
                case 'BILLING_SHIPPING_MISMATCH':
                    messageContainer.addErrorMessage({
                        message: $t("In order to use this payment method, billing and shipping address must be the same"),
                    });
                    break;
                case 'CREATE_CHECKOUT_FAILED':
                case 'GENERAL':
                    messageContainer.addErrorMessage({
                        message: $t(error.message),
                    });
                    break;
                default:
                    messageContainer.addErrorMessage({
                        message: $t('Something went wrong with your request. Please try again later.'),
                    });
            }

        }
    };
});
