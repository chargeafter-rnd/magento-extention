/*
 * ChargeAfter
 *
 * @category    Payment Gateway
 * @package     Chargeafter_Payment
 * @copyright   Copyright (c) 2021 ChargeAfter.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      taras@lagan.com.ua
 */

define(function (){
  let apiStatus=0;

  function loadApi(cdnUrl, caConfig){
    if(apiStatus===0){
      apiStatus = 1;
      !function(e,t,c,a,n){
        let r,o=t.getElementsByTagName(c)[0];
        e.ChargeAfter || (e.ChargeAfter = {}),
        t.getElementById(a)||(e.ChargeAfter.cfg = n,(r=t.createElement(c)).id=a,
          r.src=cdnUrl+"/web/v1/chargeafter.min.js?t="+1*new Date,
          r.async=!0,o.parentNode.insertBefore(r,o))}
      (window,document,"script","chargeafter-checkout-finance",caConfig);
    }
    return apiStatus;
  }

  return loadApi;
});
