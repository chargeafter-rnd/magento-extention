define([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
], function ($) {
    'use strict';
    return function (target) {
        const bodyElement = $("body");

        $.validator.addMethod(
            'required-private-prod-chargeafter-key',
            function (value) {
                return (isActive() && isProduction()) ? !(value.length < 10) : true;
            },
            $.mage.__('Please enter a Production Private API Key.')
        );
        $.validator.addMethod(
            'required-public-prod-chargeafter-key',
            function (value) {
                return (isActive() && isProduction()) ? !(value.length < 10) : true;
            },
            $.mage.__('Please enter a Production Public API Key.')
        );
        $.validator.addMethod(
            'required-private-sand-chargeafter-key',
            function (value) {
                return (isActive() && !isProduction()) ? !(value.length < 10) : true;
            },
            $.mage.__('Please enter a Sandbox Private API Key.')
        );
        $.validator.addMethod(
            'required-public-sand-chargeafter-key',
            function (value) {
                return (isActive() && !isProduction()) ? !(value.length < 10) : true;
            },
            $.mage.__('Please enter a Sandbox Public API Key.')
        );

        function isActive() {
            return bodyElement.find('.chargeafter-active-select').val() * 1;
        }

        function isProduction() {
            return bodyElement.find('.chargeafter-environment-select').val() === 'production';
        }

        return target;
    };
});
