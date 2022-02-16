let CheckoutSaveInputState = function () {
    function AddCheckoutSaveHandler() {
        jQuery(document).on('change', '#billing-new-address-form input[type="text"], #billing-new-address-form input[type="password"], #shipping-new-address-form input[type="text"]', function () {
            localStorage.setItem(jQuery(this).attr('name'), JSON.stringify({value:jQuery(this).val(), type: jQuery(this).attr('type') , form: jQuery(this).closest('form').attr('id')}));
        });
        jQuery(document).on('change', '#billing-new-address-form input[type="checkbox"], #shipping-new-address-form input[type="checkbox"]', function () {
            localStorage.setItem(jQuery(this).attr('name'), JSON.stringify({value:jQuery(this).is(':checked'), type: jQuery(this).attr('type') , form: jQuery(this).closest('form').attr('id')}));
        });
    }

    function resetFormItems() {
        Object.keys(localStorage).forEach(function (key) {
            try{
                let inputData = JSON.parse(localStorage[key]);
                var input = jQuery('#'+inputData.form+' input[name="'+key+'"]');
                switch (inputData.type) {
                    case 'password':
                    case 'text':
                        input.val(inputData.value);
                        input.closest('.field').addClass('active');
                        break;
                    case 'checkbox':
                        if((input.is(':checked') && !inputData.value) || (!input.is(':checked') && inputData.value)) {
                            input.trigger('click');
                        }
                        break;
                    default:
                        jQuery('#'+inputData.form+' select[name="'+key+'"]').val(inputData.value);
                }
            }catch (e) {
                console.log("Couldn't parse "+key)
            }
        });
    }

    function init() {
        AddCheckoutSaveHandler();
        resetFormItems();
    }
    return {
        init: init
    }
}();