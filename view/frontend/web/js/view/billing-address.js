/*
 * ChargeAfter
 *
 * @category    Payment Gateway
 * @package     Chargeafter_Payment
 * @copyright   Copyright (c) 2021 ChargeAfter.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      rnd-ua-team@chargeafter.com
 */

define([
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/address-converter',
    'Chargeafter_Payment/js/model/customer-billing-address'
],
function (
    quote,
    selectBillingAddress,
    checkoutData,
    addressConverter,
    customerBillingAddress
) {
    var mixin = {
        restoreCustomerBillingAddress: function () {
            if (customerBillingAddress.hasLastCustomerBillingAddress()) {
                const lastCustomerBillingAddress = customerBillingAddress.getLastCustomerBillingAddress();

                selectBillingAddress(lastCustomerBillingAddress);

                checkoutData.setNewCustomerBillingAddress(
                    addressConverter.quoteAddressToFormAddressData(lastCustomerBillingAddress)
                );
            }

            customerBillingAddress.setUpdate(false);
        },

        isSameBillingAddressChargeAfterPayment: function () {
            return quote.paymentMethod() &&
                   quote.paymentMethod().method === 'chargeafter' &&
                   window.checkoutConfig.payment[quote.paymentMethod().method]['isSameCustomerBillingAddress'];
        },

        useShippingAddressAsBilling() {
            this.isAddressSameAsShipping(true);
            this.useShippingAddress();

            customerBillingAddress.setUpdate(true);
        },

        canUseShippingAddress: function () {
            if (this.isSameBillingAddressChargeAfterPayment()) {
                if (!customerBillingAddress.isUpdated()) {
                    customerBillingAddress.setLastCustomerBillingAddress(
                        quote.billingAddress()
                    )
                }

                this.useShippingAddressAsBilling();
                return false;
            }

            if (customerBillingAddress.isUpdated()) {
                this.restoreCustomerBillingAddress();
            }

            return this._super();
        }
    }

    return function (target) {
        return target.extend(mixin);
    };
});
