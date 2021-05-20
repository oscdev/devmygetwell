define([
    "jquery",
    "mage/translate",
    "prototype",
    "Magento_Ui/js/modal/alert"
], function(jQuery, translate, prototype, alert){

    window.AdminPosCheckout = new Class.create();

    AdminPosCheckout.prototype = {

        initialize: function(){

            this.products = {};
            this.customer = {};
            this.shippingMethod = {};
            this.paymentMethod = {};
            this.result;
            this.quoteId = null;
            this.storeId = null;

            this.openingDialog;
            this.shippingDialog;
            this.paymentDialog;
            this.shortcutDialog;
            this.searchProductDialog;
            this.customerDialog;
            this.storeDialog;
            this.userDialog;
            this.productEditDialog;

            this.visiblePaymentFormCode = null;
        },

        reset: function()
        {
            this.products = {};
            this.customer = {};
            this.quoteId = null;
            this.refreshQuote(false);
        },

        configure: function(refreshUrl, productEmptyLayoutUrl, searchProductUrl, customerInformationUrl, createCustomerUrl, changeStoreUrl, changeUserUrl, productIdFromBarcodeUrl, saveOpeningUrl)
        {
            this.refreshUrl = refreshUrl;
            this.searchProductUrl = searchProductUrl;
            this.productEmptyLayoutUrl = productEmptyLayoutUrl;
            this.customerInformationUrl = customerInformationUrl;
            this.createCustomerUrl = createCustomerUrl;
            this.changeStoreUrl = changeStoreUrl;
            this.changeUserUrl = changeUserUrl;
            this.saveOpeningUrl = saveOpeningUrl;
            this.productIdFromBarcodeUrl = productIdFromBarcodeUrl;

            this.initEmptyProductLayout();
        },

        initEmptyProductLayout: function()
        {
            var data = {FORM_KEY: window.FORM_KEY};
            jQuery.ajax({
                url: objPosCheckout.productEmptyLayoutUrl,
                data: data,
                success: function (resp) {
                    objPosCheckout.productEmptyLayout = resp;
                },
                failure: function (resp) {
                    jQuery('#debug').html('An error occured.');
                }
            });
        },

        addProductFromBarcode: function(barcode)
        {
            var data = {barcode: barcode, FORM_KEY: window.FORM_KEY};

            jQuery.ajax({
                url: objPosCheckout.productIdFromBarcodeUrl,
                data: data,
                dataType: 'json',
                success: function (resp) {
                    if (!resp.success) {
                        objPosUi.playNok();
                        alert({content: resp.message});
                    }
                    else {
                        objPosCheckout.addProduct(resp.product_id, 1);
                    }
                },
                failure: function (resp) {
                    jQuery('#debug').html('An error occured.');
                }
            });
        },

        addProduct: function (productId, qty)
        {
            if (!this.products[productId])
                this.products[productId] = {qty: 0, options: {}, custom_price: null, ship_later: 0};

            this.products[productId].qty += qty;

            this.refreshQuote(false);
        },

        editProduct: function(productId, qty, customPrice, shipLater)
        {
            this.products[productId].qty = qty;
            this.products[productId].custom_price = customPrice;
            this.products[productId].ship_later = shipLater;

            this.productEditDialog.modal('closeModal');

            this.refreshQuote(false);
        },

        getItemByProductId: function(productId)
        {
            var returnedItem;
            this.result.items.forEach(function(item) {
                if (item.product_id == productId)
                    returnedItem = item;
            });
            return returnedItem;
        },

        showEditProductPopup: function (productId)
        {
            this.productEditDialog = jQuery('#product_edit_popup').modal({
                title: jQuery.mage.__('Edit product'),
                type: 'slide',
                buttons: []
            });

            var product = this.getItemByProductId(productId);
            jQuery('#edit_product_title').html(product.name);
            jQuery('#edit_product_sku').html(product.sku);
            jQuery('#edit_product_image').attr('src', product.image_url);
            jQuery('#edit_product_id').val(productId);

            jQuery('#edit_product_qty').val(product.qty);
            jQuery('#edit_product_qty_div').html(product.qty);

            jQuery('#edit_product_price').val(product.price);
            jQuery('#edit_product_price_div').html(product.price);

            if (product.ship_later == 1)
            {
                jQuery('#div_edit_product_ship_later').removeClass("switch-toggle-off");
                jQuery('#div_edit_product_ship_later').addClass("switch-toggle-on");
                jQuery('#edit_product_ship_later').prop('checked',true);
            }
            else
            {
                jQuery('#div_edit_product_ship_later').removeClass("switch-toggle-on");
                jQuery('#div_edit_product_ship_later').addClass("switch-toggle-off");
                jQuery('#edit_product_ship_later').prop('checked',false);
            }


            this.productEditDialog.modal('openModal');

        },

        removeProduct: function(productId)
        {
            this.products[productId].qty = 0;
            jQuery('#product_' + productId).remove();
            this.refreshQuote(false);

            var e = window.event;
            e.cancelBubble = true;
            if (e.stopPropagation)
                e.stopPropagation();
        },

        decreaseQuantity: function(productId)
        {
            this.products[productId].qty -= 1;
            if (this.products[productId].qty == 0) {
                jQuery('#product_' + productId).remove();
            }
            this.refreshQuote(false);
        },

        increaseQuantity: function(productId)
        {
            this.products[productId].qty += 1;
            this.refreshQuote(false);
        },

        selectCustomer: function(customerId)
        {
            var data = {customer_id: customerId, FORM_KEY: window.FORM_KEY};

            jQuery.ajax({
                url: this.customerInformationUrl,
                data: data,
                dataType: 'json',
                success: function (resp) {
                    if (!resp.success)
                        alert({content: resp.message});
                    else
                    {
                        jQuery('#customer_information_mode').val(resp.customer.mode);
                        jQuery('#customer_information_id').val(resp.customer.entity_id);
                        jQuery('#customer_information_email').val(resp.customer.email);
                        jQuery('#customer_information_taxvat').val(resp.customer.taxvat);
                        jQuery('#customer_information_group_id').val(resp.customer.group_id);
                        jQuery('#customer_information_website_id').val(resp.customer.website_id);

                        if (resp.address)
                        {
                            jQuery('#customer_address_firstname').val(resp.address.firstname);
                            jQuery('#customer_address_lastname').val(resp.address.lastname);
                            jQuery('#customer_address_company').val(resp.address.company);
                            jQuery('#customer_address_region').val(resp.address.region);
                            jQuery('#customer_address_country_id').val(resp.address.country_id);
                            jQuery('#customer_address_city').val(resp.address.city);
                            jQuery('#customer_address_postcode').val(resp.address.postcode);
                            jQuery('#customer_address_telephone').val(resp.address.telephone);
                        }

                        jQuery("#page_tabs_tab_current").click();

                        objPosCheckout.refreshQuote(false);
                    }
                },
                failure: function (resp) {
                    jQuery('#debug').html('An error occured.');
                }
            });
        },

        createCustomer: function()
        {
            fields = $('fieldset_create_customer').select('input', 'select', 'textarea');
            var data = Form.serializeElements(fields, true);
            jQuery.ajax({
                url: this.createCustomerUrl,
                data: data,
                dataType: 'json',
                success: function (resp) {
                    if (!resp.success) {
                        objPosUi.playNok();
                        alert({content: resp.message});
                    }
                    else
                    {
                        objPosCheckout.selectCustomer(resp.customer_id);
                        jQuery("#page_tabs_tab_current").click();
                    }
                },
                failure: function (resp) {
                    jQuery('#debug').html('An error occured.');
                }
            });
        },

        refreshQuote: function(createOrder)
        {
            jQuery('#pos_working').html('...');

            var fields = [];
            if ($('payment_form_' + objPosCheckout.paymentMethod))
                fields = $('payment_form_' + objPosCheckout.paymentMethod).select('input', 'select', 'textarea');
            var data = Form.serializeElements(fields, true);

            var customerData = {};
            fields = $('fieldset_customer_information').select('input', 'select', 'textarea');
            jQuery.each( fields, function( key, value ) {
                customerData[value.name] = value.value;
            });

            var addressData = {};
            fields = $('fieldset_customer_address').select('input', 'select', 'textarea');
            jQuery.each( fields, function( key, value ) {
                addressData[value.name] = value.value;
            });

            data.products = this.products;
            data.customer = customerData;
            data.address = addressData;
            data.quote_id = this.quoteId;
            data.store_id = this.storeId;
            data.shipping_method = this.shippingMethod;
            data.payment_method = this.paymentMethod;
            data.create_order = createOrder;
            data.create_shipment = jQuery('#pos_create_shipment').is(':checked');
            data.create_invoice = jQuery('#pos_create_invoice').is(':checked');
            data.coupon_code = jQuery('#pos_coupon_code').val();

            jQuery.ajax({
                url: this.refreshUrl,
                data: data,
                dataType: 'json',
                success: function (resp) {
                    if (!resp.success) {
                        objPosUi.playNok();
                        alert({content: resp.message});
                    }
                    else
                    {
                        objPosCheckout.result = resp;

                        if (resp.order_id)
                        {
                            //alert({title: 'Confirmation', content: resp.msg});
                            objPosCheckout.reset();
                            objPosUi.showScreen(resp.action_label, resp.action);
                        }
                        else
                        {
                            //update display
                            objPosCheckout.quoteId = objPosCheckout.result.quote_id;
                            objPosCheckout.updateDisplay();
                        }

                        objPosUi.playOk();
                    }

                    jQuery('#pos_working').html('');
                },
                failure: function (resp) {
                    jQuery('#debug').html('An error occured.');
                    jQuery('#pos_working').html('');
                }
            });

        },

        updateDisplay: function()
        {
            //totals
            //jQuery('#pos_sub_total').html(objPosCheckout.currencyFormat(this.result.totals.subtotal));
            jQuery('#pos_create_order_button').html('Pay ' + objPosCheckout.currencyFormat(this.result.totals.grand_total));

            //products list
            this.result.items.forEach(function(item) {

                //add item to screen
                if (jQuery('#product_' + item.product_id).length == 0)
                {
                    var html = objPosCheckout.getProductLayout(item);
                    if (objPosCheckout.result.items.length == 1)
                        jQuery('#products_container').html('');
                    jQuery('#products_container').html(jQuery('#products_container').html() + html);
                }

                //update details
                jQuery('#product_' + item.product_id + '_price').html(objPosCheckout.currencyFormat(item.price_incl_tax));
                jQuery('#product_' + item.product_id + '_qty').html(item.qty + 'x' + (item.ship_later == 1 ? ' <i>(Ship later)</i>' : ''));

            });

            //if quote empty
            if (this.result.items.length == 0)
                jQuery('#products_container').html('<p>&nbsp;</p><p>&nbsp;</p><center>There is no product in this cart<br>Please add products using the shortcuts button at the bottom or the search icon in the top right corner </center>');

            //customer information
            jQuery('#pos_customer').html(this.result.customer.title);

            //shipping method
            if (this.result.shipping.title)
                jQuery('#pos_shipping').html(this.result.shipping.title + ' ' + objPosCheckout.currencyFormat(this.result.shipping.grand_total));
            else
                jQuery('#pos_shipping').html('No shipping available');

            //payment method
            jQuery('#pos_payment_method').html(this.result.payment.title);
            jQuery('#div_payment_form').html('<div id="payment_form">' + this.result.payment.form + '</form>');
            jQuery('#payment_form_' + this.result.payment.method).show();

            //store & user
            jQuery('#current_store').html(this.result.store.website + '<br>' + this.result.store.group + '<br>' + this.result.store.name);
            jQuery('#current_user').html(this.result.user);

            jQuery('#pos_coupon_code').val(this.result.coupon_code);
            objPosUi.initMultiplePayment(this.result.currency_format, this.result.totals.grand_total);

        },

        getProductLayout: function(productData)
        {
            var html = this.productEmptyLayout;
            html = html.replace('{sku}', productData.sku + ' (#' + productData.product_id + ')');
            html = html.replace('{name}', productData.name);
            html = html.replace('{image_url}', productData.image_url);
            html = html.replace(new RegExp("\{id\}", "g"), productData.product_id);
            return html;
        },

        showPaymentMethodsPopup: function()
        {
            if  (!(this.result.items.length > 0))
            {
                objPosUi.playNok();
                alert({title: 'Attention', content: 'Please add products first'});
                return;
            }

            if (!this.result.shipping.method)
            {
                objPosUi.playNok();
                alert({title: 'Attention', content: 'Please select a shipping method'});
                return;
            }

            this.paymentDialog = jQuery('#payment_method_popup').modal({
                title: jQuery.mage.__('Payment'),
                type: 'slide',
                buttons: []
            });

            //populate with payment method
            jQuery('#pos_payment_buttons').html('');
            jQuery('#pos_payment_forms').html('');
            this.result.payment.available_methods.forEach(function(item) {
                var button = '<br><input type="button" id="button_payment_method_' + item.code + '" class="pos_payment_method_button" value="' + item.title + '" onclick="objPosCheckout.setPaymentMethod(\'' + item.code + '\');">';
                var form = item.form;
                jQuery('#pos_payment_buttons').html(jQuery('#pos_payment_buttons').html() + button);
                jQuery('#pos_payment_forms').html(jQuery('#pos_payment_forms').html() + form);
            });

            if (this.result.payment.available_methods.length == 0)
                jQuery('#payment_method_popup').html('No payment method available for this order.');
            else
            {
                this.showCurrentPaymentMethodForm(this.result.payment.method);
                objPosUi.initMultiplePayment(this.result.currency_format, this.result.totals.grand_total);
            }

            this.paymentDialog.modal('openModal');
        },

        showCustomerPopup: function()
        {
            this.customerDialog = jQuery('#customer_popup').modal({
                title: jQuery.mage.__('Customer'),
                type: 'slide',
                buttons: []
            });

            this.customerDialog.modal('openModal');
        },

        showStorePopup: function()
        {
            this.storeDialog = jQuery('#store_popup').modal({
                title: jQuery.mage.__('Select store'),
                type: 'slide',
                buttons: []
            });

            //highlight current store
            var fields = $('store_popup').select('input');
            jQuery.each( fields, function( key, value ) {
                if (value.id != 'button_store_' + objPosCheckout.result.store.id)
                    jQuery('#' + value.id).removeClass('pos_store_button_selected');
                else
                    jQuery('#' + value.id).addClass('pos_store_button_selected');
            });

            this.storeDialog.modal('openModal');
        },

        selectStore: function(storeId)
        {
            var data = {store_id: storeId};
            jQuery.ajax({
                url: this.changeStoreUrl,
                data: data,
                dataType: 'json',
                success: function (resp) {
                    if (!resp.success) {
                        objPosUi.playNok();
                        alert({content: resp.message});
                    }
                    else
                    {
                        objPosCheckout.storeDialog.modal('closeModal');
                        location.reload();
                    }
                },
                failure: function (resp) {
                    jQuery('#debug').html('An error occured.');
                }
            });

        },

        selectUser: function(userId)
        {
            var data = {user_id: userId};
            jQuery.ajax({
                url: this.changeUserUrl,
                data: data,
                dataType: 'json',
                success: function (resp) {
                    if (!resp.success)
                    {
                        alert({content: resp.message});
                        objPosUi.playNok();
                    }
                    else
                    {
                        objPosUi.playOk();
                        objPosCheckout.userDialog.modal('closeModal');
                        location.reload();
                    }
                },
                failure: function (resp) {
                    jQuery('#debug').html('An error occured.');
                }
            });
        },

        showUserPopup: function()
        {
            this.userDialog = jQuery('#user_popup').modal({
                title: jQuery.mage.__('Select user'),
                type: 'slide',
                buttons: []
            });

            //highlight current user
            var fields = $('user_popup').select('input');
            jQuery.each( fields, function( key, value ) {
                if (value.id != 'button_user_' + objPosCheckout.result.user.id)
                    jQuery('#' + value.id).removeClass('pos_user_button_selected');
                else
                    jQuery('#' + value.id).addClass('pos_user_button_selected');
            });

            this.userDialog.modal('openModal');
        },

        setPaymentMethod: function(method)
        {
            this.paymentMethod = {method: method};
            this.showCurrentPaymentMethodForm(method);
        },

        showCurrentPaymentMethodForm: function(method)
        {
            var divId = 'payment_form_' + method;

            if (this.visiblePaymentFormCode)
            {
                jQuery('#payment_form_' + this.visiblePaymentFormCode).hide();
                jQuery('#button_payment_method_' + this.visiblePaymentFormCode).removeClass('pos_payment_method_button_selected');
                this.visiblePaymentFormCode = null;
            }

            if (jQuery('#' + divId)) {
                jQuery('#button_payment_method_' + method).addClass('pos_payment_method_button_selected');
                jQuery('#' + divId).show();
                this.visiblePaymentFormCode = method;
                this.paymentMethod = method;
            }
        },

        showShippingMethodsPopup: function()
        {
            this.shippingDialog = jQuery('#shipping_method_popup').modal({
                title: jQuery.mage.__('Select shipping method'),
                type: 'slide',
                buttons: []
            });

            //populate with shipping method
            jQuery('#shipping_method_popup').html('');
            this.result.shipping.available_methods.forEach(function(item) {
                var style = 'pos_shipping_method_button';
                if (item.method == objPosCheckout.result.shipping.method)
                    style += ' pos_payment_method_button_selected';
                var html = '<br><input type="button" class="' + style + '" value="' + item.title + '" onclick="objPosCheckout.setShippingMethod(\'' + item.method + '\');">';
                jQuery('#shipping_method_popup').html(jQuery('#shipping_method_popup').html() + html);
            });

            if (this.result.shipping.available_methods.length == 0)
                jQuery('#shipping_method_popup').html('No rates available for this order.');

            this.shippingDialog.modal('openModal');
        },

        setShippingMethod: function(method)
        {
            this.shippingMethod = {method: method};
            this.refreshQuote(false);
            this.shippingDialog.modal('closeModal');
        },

        showShortcutsPopup: function()
        {
            this.shortcutDialog = jQuery('#shortcuts_popup').modal({
                title: jQuery.mage.__('Shortcuts'),
                type: 'slide',
                buttons: []
            });

            this.shortcutDialog.modal('openModal');
        },

        closeShortcutPopup: function()
        {
            this.shortcutDialog.modal('closeModal');
        },

        showSearchProductPopup: function()
        {
            this.searchProductDialog = jQuery('#search_product_popup').modal({
                title: jQuery.mage.__('Search for products'),
                type: 'slide',
                buttons: []
            });

            this.searchProductDialog.modal('openModal');
            objPosUi.playOk();
            setTimeout(function(){ jQuery('#pos_search_product_field').focus(); }, 500);
        },

        showOpeningPopup: function()
        {
            objPosUi.showNumericPopup('Enter opening value', '', function(value) {objPosCheckout.saveOpeningValue(value);});
        },

        saveOpeningValue: function(value)
        {
            if (jQuery('#stat_payment_opening'))
                jQuery('#stat_payment_opening').html(value);

            var data = {opening_value: value};
            jQuery.ajax({
                url: this.saveOpeningUrl,
                data: data,
                success: function (resp) {

                }
            });
        },

        closeSearchProductPopup: function()
        {
            this.searchProductDialog.modal('closeModal');
        },

        displaySearchResults: function()
        {
            var searchString = jQuery('#pos_search_product_field').val();
            if (searchString.length <= 2)
                return;

            var data = {search_string: searchString};
            jQuery.ajax({
                url: this.searchProductUrl,
                data: data,
                success: function (resp) {
                    jQuery('#pos_search_results').html(resp);
                }
            });
        },

        commit: function()
        {
            this.paymentDialog.modal('closeModal');
            this.refreshQuote(true);
        },

        currencyFormat: function(value)
        {
            value = parseFloat(value).toFixed(2);
            value = this.result.currency_format.replace('%s', value);
            return value;
        },

        applyCustomerMode: function()
        {
            switch(jQuery('#customer_information_mode').val())
            {
                case 'guest':
                    objPosCheckout.selectCustomer('guest');
                    break;
            }
        }

    };

});
