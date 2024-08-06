/*
 * ChargeAfter
 *
 * @category    Payment Gateway
 * @package     Chargeafter_Payment
 * @copyright   Copyright (c) 2021 ChargeAfter.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      taras@lagan.com.ua
 */

define(function () {
  let isLoaded=0;

  function loadApi(cdnUrl, caConfig){
    if(isLoaded === 0) {
      isLoaded = 1;

      function onLoadChargeAfterSDKScript() {
          ChargeAfter.init(caConfig);
      }

      var script = document.createElement('script');
      script.src = cdnUrl + '/web/v2/chargeafter.min.js?t=' + Date.now();
      script.type = 'text/javascript';
      script.async = true;
      script.onload = onLoadChargeAfterSDKScript;
      document.body.appendChild(script);
    }

    return isLoaded;
  }

  return loadApi;
});
