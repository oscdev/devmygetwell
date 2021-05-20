require(
    [
        'jquery',
        'jquery/ui',
        'jquery/validate',
        'mage/translate'
    ],
    function ($) {
        //Validate Image Extensions
        $.validator.addMethod(
            'validate-fileextensions',
            function (v, elm) {

                var extensions = ['jpeg', 'jpg', 'png'];
                if (!v) {
                    return true;
                }
                with(elm) {
                    var ext = value.substring(value.lastIndexOf('.') + 1), i;
                    for (i = 0; i < extensions.length; i++) {
                        if (ext == extensions[i]) {
                            return true;
                        }
                    }
                }
                return false;
            },
            $.mage.__('Disallowed file type.')
        );
    }
);
