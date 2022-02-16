define([
    'jquery',
    'mage/translate'
], function($, $t){
    function showWarning() {
        $('.emptysubmit').fadeIn().promise().done(function () {

        })
    }
    function hideWarning() {
        $('.emptysubmit').fadeOut();
    }

    function hideRadio() {
        return $('.formoptionwrap > .questionwrap, .formoptionwrap .radiowrap').fadeOut().promise();
    }

    function showForm() {
        $('body').addClass('formtime');
        $('#formholder').fadeIn();
    }

    function clearSelection() {
        $('#formholder .field, #formholder input, #ordernote').remove();
    }

    function createDropdown(data, element, name) {
        if (!data.length) {
            element.css('display', 'none');
            return;
        } else if (name == 'product') {
            $('.field').last().remove();
            name = 'order_id'
        }
        let select = $('<select required name="' + name + '"></select>');
        for (let i = 0; i < data.length; i++) {
            let option = null;
            if(name === 'order_id') {
                option = $('<option value="' + data[i].value + '">' + $t(data[i].text) + '</option>')
            }else {
                option = $('<option value="' + data[i][0] + '">' + $t(data[i][1]) + '</option>');
            }
            select.append(option)
        }
        return select;
    }

    function hideForm() {
        return $('#formholder').fadeOut().promise();
    }

    function showRadio() {
        $('body').removeClass('formtime');
        $('.formoptionwrap > .questionwrap, .formoptionwrap .radiowrap').fadeIn();
    }

    otherQuestion = function(){
        $('.contact-form-container > .question-container').hide();
        $('.contact-form-container > .login').hide();
        $('#login_text').hide();
        $('#topic_other').prop('checked', true);
        $('.txt_direct_support').hide();
        $('.next').trigger('click');
    }

    $(document).on('click', '.top span', function () {
        $('.contact-form-container > .question-container').show();
        $('.contact-form-container > .login').show();
        $('#login_text').show();
        $('#topic_other').prop('checked', false);
        $('#formholder .errormessage').fadeOut();
        saveValues();
        $('#login_text').show();
        $('.txt_direct_support').show();
        hideForm().then(showRadio());
    });

    function saveValues() {
        let element = $('#issueform')
        var fields = $('.field');
        var saveVals = element.data('save') || {};
        for (var i = 0; i < fields.length; i++) {
            saveVals[$(fields[i]).find('.added').text()] = $(fields[i]).find('input, select, textarea').val()
        }
        element.data('save', saveVals)
    }

    $('.login').on('click', function (e) {
        e.preventDefault();
        $('#contactoverlay').fadeIn();
    });

    $('.question-tooltip').on('click', function (e) {
        if ($(this).hasClass('active')) {
            $(this).siblings('.question-tooltip-message').hide();
        } else {
            $(this).siblings('.question-tooltip-message').css('display', 'inline-block')
        }
        $(this).toggleClass('active');
    });

    $(document).on('click', '#issuesubmit', function (e) {
        e.preventDefault();
        uploadFields();
    });

    function addFileListener() {
        $(document).on('change', '#fileupload', function (e) {
            $('#readeroutput').text("");
            $(this).parent().removeClass('fillin');
            handleFileSelect(e)
        })
    }

    function handleFileSelect(evt) {
        var files = evt.target.files; // FileList object
        for (var i = 0, f; f = files[i]; i++) {
            $('#readeroutput').text(f.name);
        }
    }

    function getOrderdata() {
        let orderData = [];
        let orders = $('.orderdata');
        for (let i = 0; i < orders.length; i++) {
            orderData.push({
                value: $(orders[i]).data('ordernr'),
                text: "#" + $(orders[i]).data('ordernr') + "    (" + $(orders[i]).data('created').split(' ')[0] + ")"
            });
        }
        return orderData;
    }

    function createOrderDropdown(element) {
        let orderdata = getOrderdata();

        let select = $('<select></select>');
        for (let i = 0; i < orderdata.length; i++) {
            let option = $('<option value="#' + orderdata[i].number + '">#' + orderdata[i].number + ' (' + orderdata[i].time + ')</option>');
            select.append(option)
        }
        return select;
    }

    function uploadFields() {
        let fields = $('#issueform input, #issueform textarea, #issueform select');
        // Submit also in the fields, so -1 to skip it
        for (let i = 0; i < fields.length - 1; i++) {
            if ($(fields[i]).prop('required') === true && $(fields[i]).val().length === 0) {
                $('#formholder .errormessage').fadeIn();
                // Look for upcoming empty fields, so start from i
                for (i; i < fields.length - 1; i++) {
                    if ($(fields[i]).prop('required') === true && $(fields[i]).val().length === 0) {
                        $(fields[i]).addClass('fillin');
                    }
                    // Ugly way to give the parent of the custom file input a red outline
                    if ($('#fileupload').hasClass('fillin')) {
                        $('#fileupload').parent().addClass('fillin');
                    }
                }
                return;
            }
        }
        $('#issuesubmit').attr('disabled', 'true');
        $('#issueform').submit();
    }

    return function (params, element) {
        let configs = params.configs;
        let cancelationReasons = params.cancelationReasons;
        let shipmentissues = params.shipmentissues;
        let issues = params.issues;

        let lastIndex;
        $('.next').on('click', function () {
            let checked = $('.formoptionwrap input:checked');
            if (checked.length === 0) {
                showWarning();
            } else {
                hideWarning();
                $('#login_text').hide();
                let checked_id = $('.formoptionwrap input:checked').attr('id');
                let index = $('.formoptionwrap > .radiowrap input').index(checked);
                if(checked_id == 'topic_other'){
                    index = 3;
                }
                index == false ? $('.column.main').css('min-height', '870px') : $('.column.main').css('min-height', '770px')
                $(this).data({
                    'text': $(checked.parent()[0]).data('label'),
                    'translated': $(checked.parent()[0]).text()
                });
                if (lastIndex === index) {
                    hideRadio().then(showForm());
                } else {
                    let formConfig = configs[index];
                    clearSelection();
                    buildForm(formConfig, issues, cancelationReasons,shipmentissues);
                    hideRadio().then(showForm());
                    lastIndex = index;
                }
            }
        });

        function buildForm(config, issues, cancelationReasons, shipmentissues) {
            const form = $('#issueform');
            let subject = $('.next').data('text');
            let translated = $('.next').data('translated');
            $('#formholder #selected').text(translated);
            if(subject == 'Other'){
                form.append($('<input style="display:none;" name="subject" type="text" value="' + subject + '"/>'));
            }else{
                form.append($('<input name="subject" id="subject" type="text" value="' + subject + '"/>'));
            }
            for (let i = 0; i < config.length; i++) {
                let required = config[i].required ? 'required="' + config[i].required + '"' : "";
                let field = $('<div class="field ' + config[i].required + '"></div>');
                field.append($('<p class="added">' + config[i].label + '</p>'));
                let placeholder = config[i].placeholder ? config[i].placeholder : "";
                let readonly = config[i].readonly ? 'readonly' : "";

                switch (config[i].type) {
                    case "textarea":
                        field.append($('<textarea name="' + config[i].name + '" maxlength="200" placeholder="' + placeholder + '" '+ readonly +'></textarea>'));
                        break;
                    case "file":
                        field.append($('<div class="verflex"><label class="custom-file-input"><input id="fileupload" accept="image/png, image/jpeg" '+required+' name="' + config[i].name + '" type="' + config[i].type + '" />' + params.uploadLabel + '</label><span id="readeroutput"></span><span class="uploadtext">' + params.uploadText + '</span></div>'));
                        addFileListener();
                        break;
                    case "product":
                        field.append(createDropdown(getOrderdata(), field, 'product'));
                        break;
                    case "shipmentissues":
                        field.append(createDropdown(shipmentissues, field, 'shipmentissues'));
                        break;
                    case "issue":
                        field.append(createDropdown(issues, field, 'issue'));
                        break;
                    case "cancel":
                        field.append(createDropdown(cancelationReasons, field, 'reason_cancel'));
                        break;
                    case 'dropdown':
                        field.append(createDropdown(config[i].values, field, config[i].name));
                        break;
                    default:
                        let value = config[i].value || "";
                        field.append($('<input ' + required + ' value="' + value + '" name="' + config[i].name + '" type="' + config[i].type + '" placeholder="' + placeholder + '" '+ readonly +'/>'));
                }
                // Check to see if the input value have been filled in before and reset them
                form.data('save') && form.data('save')[config[i].label] ? field.find('input,select, textarea').val(form.data('save')[config[i].label]) : null;
                form.append(field);
            }
            if (subject === 'Cancel order') {
                let note = $('<span id="ordernote">' + params.pleaseNoteText + '</span>');
                $('.field').last().css('margin-bottom', '48px');
                form.append(note);
            }
            let submit = $('<input type="submit" id="issuesubmit" value="' + params.submitText + '"/>')
            form.append(submit)
        }
    }
})
