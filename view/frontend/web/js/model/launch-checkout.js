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
    'jquery',
    'Chargeafter_Payment/js/model/error-processor',
], function ($, errorProcessor) {
    'use strict';

    return function (options, messageContainer) {

        return $.Deferred(function (deferred){
            options.callback = function (token, data, error) {
                if(error){
                    deferred.reject(error);
                }else{
                    deferred.resolve({token, data});
                }
            };
            ChargeAfter.checkout.present(options);
        }).fail(
            function (error) {
                errorProcessor.process(error, messageContainer);
            }
        )

    };
});
