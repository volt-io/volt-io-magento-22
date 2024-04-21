define(function () {
    'use strict';

    const config = window.checkoutConfig.payment.volt;

    return {
        isActive: config.isActive ?? false,
        isSandbox: config.isSandbox ?? true,
        title: config.title || '',
        redirectUrl: config.redirectUrl || '',
        logoUrl: config.logoUrl || '',
    };
});
