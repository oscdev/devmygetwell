define([
    "jquery",
    "mage/translate",
    "prototype"
], function(jQuery, confirm, alert){

    window.AdminPosUi = new Class.create();

    jQuery('#html-body').delegate("[data-action=switch-toggle]", "click", function (e) {
        e.preventDefault();

        checkboxInputId = jQuery(this).data('checkbox-id');
        if (jQuery(this).hasClass("switch-toggle-on"))
        {
            jQuery(this).removeClass("switch-toggle-on");
            jQuery(this).addClass("switch-toggle-off");
            jQuery('#' + checkboxInputId).prop( "checked", false);
        }
        else
        {
            jQuery(this).removeClass("switch-toggle-off");
            jQuery(this).addClass("switch-toggle-on");
            jQuery('#' + checkboxInputId).prop( "checked", true);
        }

    });

    AdminPosUi.prototype = {

        initialize: function(){
            this.popup = null;
            this.numericPopup = null;
            this.numericPopupCallBack;

            this.displayTime();
            setInterval(function(){ objPosUi.displayTime(); }, 60000);
        },

        displayTime: function()
        {
            d = new Date();
            var minutes = d.getMinutes();
            if (minutes.toString().length == 1)
                minutes = '0' + minutes;
            var hours = d.getHours();
            if (hours.toString().length == 1)
                hours = '0' + hours;
            jQuery('#pos_time').html(hours + ":" + minutes);
        },

        //ajax display for screen url
        showScreen: function(title, screenUrl)
        {
            jQuery('#pos-page-title').html(title);
            jQuery('#pos-content').html('Loading...');

            var data = {
                            FORM_KEY: window.FORM_KEY
                        };

            jQuery.ajax({
                url: screenUrl,
                data: data,
                success: function (resp) {
                    jQuery('#pos-content').html(resp);
                },
                failure: function (resp) {
                    jQuery('#pos-content').html('An error occured.');
                }
            });

        },

        showPopup: function(title, screenUrl)
        {
            this.popup = jQuery('#pos_abstract_popup').modal({
                title: jQuery.mage.__(title),
                type: 'slide',
                closeExisting: false,
                buttons: []
            });

            var data = {
                FORM_KEY: window.FORM_KEY
            };

            jQuery.ajax({
                url: screenUrl,
                data: data,
                success: function (resp) {
                    jQuery('#pos_abstract_popup').html(resp);
                    objPosUi.popup.modal('openModal');
                    objPosUi.playOk();
                }
            });
        },

        showNumericPopup: function(title, fullValue, callback)
        {
            this.numericPopup = jQuery('#pos_numeric_popup').modal({
                title: title,
                type: 'slide',
                closeExisting: false,
                buttons: []
            });

            jQuery('#pos_numeric_popup_value').html('');
            jQuery('#pos_numeric_custom_button').val(fullValue);

            this.numericPopupCallBack = callback;
            this.numericFullValue = fullValue;

            this.numericPopup.modal('openModal');

            objPosUi.playOk();
        },

        applyStatSettings: function()
        {
            var form = jQuery('#frm_stat');
            fields = $('frm_stat').select('input', 'select', 'textarea');
            var data = Form.serializeElements(fields, true);

            jQuery.ajax({
                url: form.attr('action'),
                data: data,
                success: function (resp) {
                    jQuery('#menu-magento-backend-report').click();
                }
            });
        },

        numericPopupClick: function(char)
        {
            char = char.toLowerCase();
            switch(char)
            {
                case '.':
                case '0':
                case '1':
                case '2':
                case '3':
                case '4':
                case '5':
                case '6':
                case '7':
                case '8':
                case '9':
                    jQuery('#pos_numeric_popup_value').html(jQuery('#pos_numeric_popup_value').html() + char);
                    break;
                case 'ok':
                    var value = (jQuery('#pos_numeric_popup_value').html());
                    if (!value)
                        value = 0;
                    value = parseFloat(value);
                    if (this.numericPopupCallBack)
                        this.numericPopupCallBack(value);
                    this.numericPopup.modal('closeModal');
                    objPosUi.playOk();
                    break;
                case 'clear':
                    jQuery('#pos_numeric_popup_value').html('');
                    break;
                case 'pos_numeric_custom_button':
                    jQuery('#pos_numeric_popup_value').html(this.numericFullValue);
                    this.numericPopupClick('ok');
                    break;
            }
        },

        hidePopup: function()
        {
            this.popup.modal('closeModal');
        },

        initMultiplePayment: function(currencyFormat, grandTotal)
        {
            this.currency_format = currencyFormat;
            this.grandTotal = grandTotal;

            jQuery('#multiple_total_due').html(this.currencyFormat(this.grandTotal));

            this.updateMultipleTotals();
        },

        updateMultipleTotals: function()
        {
            jQuery('#multiple_total_paid').html(this.currencyFormat(this.getTotalPaid()));

            var balance = this.getTotalDue();
            var color = 'black';
            if (balance == 0)
                color = 'green';
            else
                color = 'red';

            jQuery('#multiple_total_balance').html('<font color="' + color + '">' + this.currencyFormat(balance) + '</font>');
        },

        getTotalPaid: function()
        {
            var totalPaid = 0;
            if ($('payment_form_multiple_payment'))
            {
                fields = $('payment_form_multiple_payment').select('input');
                jQuery.each( fields, function( key, value ) {
                    if (value.value)
                        totalPaid += parseFloat(value.value);
                });
            }
            return parseFloat(totalPaid).toFixed(2);
        },

        getTotalDue: function()
        {
            return parseFloat(this.grandTotal - this.getTotalPaid()).toFixed(2);
        },

        currencyFormat: function(value)
        {
            value = parseFloat(value).toFixed(2);
            value = this.currency_format.replace('%s', value);
            return value;
        },

        playOk: function()
        {
            jQuery("#audio_ok").get(0).play();
        },

        playNok: function ()
        {
            jQuery("#audio_nok").get(0).play();
        }
    };

});
