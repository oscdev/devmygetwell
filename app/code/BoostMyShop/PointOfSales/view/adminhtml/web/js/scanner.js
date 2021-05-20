define([
    "jquery",
    "mage/translate",
    "prototype"
], function(jQuery, confirm, alert){

    window.AdminScanner = new Class.create();

    AdminScanner.prototype = {

        initialize: function(){
            this.barcode = "";
            this.displayInput = null;
            this.callback = null;
            this.lastInputTimeStamp = null;

            document.onkeypress = this.handleKey;
        },

        enable: function(displayInput, callback)
        {
            this.displayInput = displayInput;
            this.callback = callback;
        },

        handleKey: function (evt) {

            //Dont process event if focuses control is textbox OR textarea
            if (document.activeElement)
            {
                if ((document.activeElement.type == 'text') || (document.activeElement.tagName.toLowerCase() == 'textarea'))
                    return true;
            }

            var evt = (window.event ? window.event : evt);
            var keyCode;

            keyCode = evt.which;//FF : OK, Chrome OK, IE: NOK
            if(!keyCode) keyCode = evt.keyCode;//FF : NOK, Chrome OK, IE: OK
            if(!keyCode) keyCode = evt.charCode;//FF : OK, Chrome OK, IE: NOK

            if (keyCode != 13)
            {
                objScanner.barcode += String.fromCharCode(keyCode);
                if (objScanner.displayInput != null)
                    objScanner.displayInput.value = objScanner.barcode;
            }
            else
            {
                if (objScanner.callback)
                    objScanner.callback(objScanner.barcode);
                objScanner.barcode = '';
            }

            this.lastInputTimeStamp = new Date().getTime();

            return false;
        }


    };

});
