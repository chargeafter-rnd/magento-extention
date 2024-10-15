/*
 * ChargeAfter
 *
 * @category    Payment Gateway
 * @package     Chargeafter_Payment
 * @copyright   Copyright (c) 2021 ChargeAfter.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      taras@lagan.com.ua
 */

define([
    'jquery',
    'mageUtils',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/create-billing-address',
    'Magento_Checkout/js/action/create-shipping-address',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/checkout-data',
    'Magento_Customer/js/customer-data',
    'uiRegistry',
], function (
    $,
    utils,
    quote,
    createBillingAddress,
    createShippingAddress,
    selectBillingAddress,
    selectShippingAddress,
    setShippingInformationAction,
    checkoutData,
    customerData,
    registry
) {
    'use strict';

    const countryData = customerData.get('directory-data')();

    const getRegionByCode = function (regionCode, countryId) {
        const regions = countryData[countryId]?.regions;

        if (regions) {
            for (const regionId in regions) {
                if (regions[regionId].code === regionCode) {
                    return {
                        id: regionId,
                        code: regionCode,
                        name: regions[regionId].name
                    };
                }
            }
        }

        return null;
    }

    const mapAddressData = function (address) {
        const addressData = [];
        const street = [];

        let hasValidationError = false;

        $.each([
            'firstName',
            'lastName',
            'line1',
            'state',
            'city',
            'zipCode',
        ], function (index, requiredParam) {
            if (
                address.hasOwnProperty(requiredParam) &&
                utils.isEmpty(address[requiredParam])
            ) {
                hasValidationError = true;
                return false;
            }
        });

        if (!hasValidationError) {
            const countryId = address.countryId || 'US';

            addressData['firstname'] = address.firstName;
            addressData['lastname'] = address.lastName;

            if (!utils.isEmpty(address.email)) {
                addressData['email'] = address.email;
            }

            if (!utils.isEmpty(address.mobilePhoneNumber)) {
                addressData['telephone'] = address.mobilePhoneNumber;
            }

            addressData['street'] = {
                0: !utils.isEmpty(address.line1) ? address.line1 : '',
                1: !utils.isEmpty(address.line2) ? address.line2 : ''
            };

            addressData['city'] = address.city;
            addressData['postcode'] = address.zipCode;

            const region = getRegionByCode(address.state, countryId);
            if (region) {
                addressData['region'] = region.name;
                addressData['region_id'] = region.id;
                addressData['region_code'] = region.code;
            }

            addressData['save_in_address_book'] = 0;
            addressData['country_id'] = countryId;

            if (!utils.isEmpty(address.company)) {
                addressData['company'] = address.company;
            }

            if (!utils.isEmpty(address.customerId)) {
                addressData['customerId'] = address.customerId;
            }

            if (!utils.isEmpty(address.vatId)) {
                addressData['vatId'] = address.vatId;
            }

            return addressData;
        }

        return null;
    };

    const verifyAddressDataChanges = function (addressData, address) {
        if (!addressData || !address) {
            return false;
        }

        return (
            addressData.firstname !== address.firstname ||
            addressData.lastname !== address.lastname ||
            addressData.street[0] !== address.street[0] ||
            addressData.street[1] !== address.street[1] ||
            addressData.city !== address.city ||
            addressData.postcode !== address.postcode ||
            addressData.region !== address.region ||
            addressData.region_id !== address.region_id ||
            addressData.region_code !== address.region_code
        );
    };

    return function (options) {
        const consumerDetails = options?.consumerDetails || {};

        const billingAddress = consumerDetails?.billingAddress || null;
        const shippingAddress = consumerDetails?.shippingAddress || null;

        delete consumerDetails.billingAddress;
        delete consumerDetails.shippingAddress;

        if (billingAddress) {
            const quoteBillingAddress = quote.billingAddress();

            const addressData = mapAddressData({
                ...consumerDetails,
                ...billingAddress,
                company: quoteBillingAddress.company,
                countryId: quoteBillingAddress.countryId,
                customerId: quoteBillingAddress.customerId,
                vatId: quoteBillingAddress.vatId
            });

            if (verifyAddressDataChanges(addressData, quoteBillingAddress)) {
                const newBillingAddress = createBillingAddress(addressData);

                selectBillingAddress(newBillingAddress);

                checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                checkoutData.setNewCustomerBillingAddress($.extend(true, {}, addressData));
                checkoutData.setBillingAddressFromData($.extend(true, {}, addressData));

                // Update UI components
                registry.async('checkoutProvider')(function (checkoutProvider) {
                    const paymentMethodCode = checkoutData.getSelectedPaymentMethod();
                    const billingAddressCode = "billingAddress" + paymentMethodCode;

                    const defaultAddressData = checkoutProvider.get(billingAddressCode);

                    if (defaultAddressData === undefined) {
                        // Skip if payment does not have a billing address form
                        return;
                    }

                    const billingAddressData = checkoutData.getBillingAddressFromData();

                    if (billingAddressData) {
                        checkoutProvider.set(
                            billingAddressCode,
                            $.extend(true, {}, defaultAddressData, billingAddressData)
                        );
                    }
                });
            }
        }

        if (shippingAddress) {
            const quoteShippingAddress = quote.shippingAddress();

            const addressData = mapAddressData({
                ...consumerDetails,
                ...shippingAddress,
                company: quoteShippingAddress.company,
                countryId: quoteShippingAddress.countryId,
                customerId: quoteShippingAddress.customerId,
                vatId: quoteShippingAddress.vatId
            });

            if (verifyAddressDataChanges(addressData, quoteShippingAddress)) {
                const newShippingAddress = createShippingAddress(addressData);

                selectShippingAddress(newShippingAddress);

                checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                checkoutData.setNewCustomerShippingAddress($.extend(true, {}, addressData));
                checkoutData.setShippingAddressFromData($.extend(true, {}, addressData));

                // Update UI components
                registry.async('checkoutProvider')(function (checkoutProvider) {
                    const shippingAddressData = checkoutData.getShippingAddressFromData();

                    if (shippingAddressData) {
                        checkoutProvider.set(
                            'shippingAddress',
                            $.extend(true, {}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                        );
                    }
                });

                // Update shipping information
                setShippingInformationAction();
            }
        }
    };
});
