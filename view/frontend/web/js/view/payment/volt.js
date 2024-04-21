define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
],
function (
    Component,
    rendererList
) {
    'use strict';
    rendererList.push(
        {
            type: 'volt',
            component: 'Volt_Payment/js/view/payment/method-renderer/volt'
        }
    );
    return Component.extend({});
});
