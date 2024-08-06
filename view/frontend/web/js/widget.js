/*
 * ChargeAfter
 *
 * @category    Payment Gateway
 * @package     Chargeafter_Payment
 * @copyright   Copyright (c) 2021 ChargeAfter.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      taras@lagan.com.ua
 */

define(['Chargeafter_Payment/js/model/load-api'], function(loadApi) {
    return function ({cdnUrl, caConfig}){
        loadApi(cdnUrl, caConfig);
    }
})
