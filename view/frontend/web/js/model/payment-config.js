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

    const configName = 'chargeafter';

    return {
        getConfig: function (key) {
            return window.checkoutConfig.payment[configName][key];
        }
    }
});
