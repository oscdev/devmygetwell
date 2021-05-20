define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
],function(Component,renderList){
    'use strict';
    renderList.push({
        type : 'ccavenue',
        component : 'Bluethink_Ccavenue/js/view/payment/method-renderer/ccavenue-method'
    });

    return Component.extend({});
})
