define([
    'jquery',
    'mage/translate',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/action/redirect-on-success',
    'Volt_Payment/js/model/volt-config',
    'Magento_Ui/js/modal/modal',
], function (
    $,
    $t,
    Component,
    redirectOnSuccessAction,
    config,
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Volt_Payment/payment/volt',
            logoUrl: config.logoUrl,
        },

        modalSelector: '#volt-modal',
        modalOptions: {
            type: 'popup',
            wrapperClass: 'volt-modal-wrapper',
            responsive: true,
            title: false,
            buttons: [],
        },

        getCode: function() {
            return this.item.method;
        },

        getData: function() {
            return {
                'method': this.item.method,
            };
        },

        getTitle: function() {
            return $t('Pay by Bank');
        },

        afterPlaceOrder: function () {
            redirectOnSuccessAction.redirectUrl = config.redirectUrl;
        },

        // Modal
        initializeModal: function () {
            $(this.modalSelector).modal(this.modalOptions);

            let self = this;
            // Add custom ESC key event
            $(document).on('keydown', function (event) {
                if (event.keyCode === 27) {
                    self.closeModal();
                }
            });
        },

        openModal: function () {
            $(this.modalSelector).modal('openModal');
        },

        closeModal: function () {
            $(this.modalSelector).modal('closeModal');
        },
    });
});
