define(["jquery", "jquery-ui-modules/effect-slide"],
    function($){
        "use strict";
        const popup = $('#giftpopup');
        const previewOverlay = $('.previewoverlay');
        const overlay = $('.giftoverlay');
        const button = $('.giftpopupbutton');
        var textarea = isMobile() ? $('#giftsection #cardtext') : $('#cardsection #cardtext');
        let lastCursorPos = 0;
        const shouldShowPopup = popup.data('show');

        const emojiRegex = /\uD83C\uDFF4(?:\uDB40\uDC67\uDB40\uDC62(?:\uDB40\uDC65\uDB40\uDC6E\uDB40\uDC67|\uDB40\uDC77\uDB40\uDC6C\uDB40\uDC73|\uDB40\uDC73\uDB40\uDC63\uDB40\uDC74)\uDB40\uDC7F|\u200D\u2620\uFE0F)|\uD83D\uDC69\u200D\uD83D\uDC69\u200D(?:\uD83D\uDC66\u200D\uD83D\uDC66|\uD83D\uDC67\u200D(?:\uD83D[\uDC66\uDC67]))|\uD83D\uDC68(?:\u200D(?:\u2764\uFE0F\u200D(?:\uD83D\uDC8B\u200D)?\uD83D\uDC68|(?:\uD83D[\uDC68\uDC69])\u200D(?:\uD83D\uDC66\u200D\uD83D\uDC66|\uD83D\uDC67\u200D(?:\uD83D[\uDC66\uDC67]))|\uD83D\uDC66\u200D\uD83D\uDC66|\uD83D\uDC67\u200D(?:\uD83D[\uDC66\uDC67])|\uD83C[\uDF3E\uDF73\uDF93\uDFA4\uDFA8\uDFEB\uDFED]|\uD83D[\uDCBB\uDCBC\uDD27\uDD2C\uDE80\uDE92]|\uD83E[\uDDB0-\uDDB3])|(?:\uD83C[\uDFFB-\uDFFF])\u200D(?:\uD83C[\uDF3E\uDF73\uDF93\uDFA4\uDFA8\uDFEB\uDFED]|\uD83D[\uDCBB\uDCBC\uDD27\uDD2C\uDE80\uDE92]|\uD83E[\uDDB0-\uDDB3]))|\uD83D\uDC69\u200D(?:\u2764\uFE0F\u200D(?:\uD83D\uDC8B\u200D(?:\uD83D[\uDC68\uDC69])|\uD83D[\uDC68\uDC69])|\uD83C[\uDF3E\uDF73\uDF93\uDFA4\uDFA8\uDFEB\uDFED]|\uD83D[\uDCBB\uDCBC\uDD27\uDD2C\uDE80\uDE92]|\uD83E[\uDDB0-\uDDB3])|\uD83D\uDC69\u200D\uD83D\uDC66\u200D\uD83D\uDC66|(?:\uD83D\uDC41\uFE0F\u200D\uD83D\uDDE8|\uD83D\uDC69(?:\uD83C[\uDFFB-\uDFFF])\u200D[\u2695\u2696\u2708]|\uD83D\uDC68(?:(?:\uD83C[\uDFFB-\uDFFF])\u200D[\u2695\u2696\u2708]|\u200D[\u2695\u2696\u2708])|(?:(?:\u26F9|\uD83C[\uDFCB\uDFCC]|\uD83D\uDD75)\uFE0F|\uD83D\uDC6F|\uD83E[\uDD3C\uDDDE\uDDDF])\u200D[\u2640\u2642]|(?:\u26F9|\uD83C[\uDFCB\uDFCC]|\uD83D\uDD75)(?:\uD83C[\uDFFB-\uDFFF])\u200D[\u2640\u2642]|(?:\uD83C[\uDFC3\uDFC4\uDFCA]|\uD83D[\uDC6E\uDC71\uDC73\uDC77\uDC81\uDC82\uDC86\uDC87\uDE45-\uDE47\uDE4B\uDE4D\uDE4E\uDEA3\uDEB4-\uDEB6]|\uD83E[\uDD26\uDD37-\uDD39\uDD3D\uDD3E\uDDB8\uDDB9\uDDD6-\uDDDD])(?:(?:\uD83C[\uDFFB-\uDFFF])\u200D[\u2640\u2642]|\u200D[\u2640\u2642])|\uD83D\uDC69\u200D[\u2695\u2696\u2708])\uFE0F|\uD83D\uDC69\u200D\uD83D\uDC67\u200D(?:\uD83D[\uDC66\uDC67])|\uD83D\uDC69\u200D\uD83D\uDC69\u200D(?:\uD83D[\uDC66\uDC67])|\uD83D\uDC68(?:\u200D(?:(?:\uD83D[\uDC68\uDC69])\u200D(?:\uD83D[\uDC66\uDC67])|\uD83D[\uDC66\uDC67])|\uD83C[\uDFFB-\uDFFF])|\uD83C\uDFF3\uFE0F\u200D\uD83C\uDF08|\uD83D\uDC69\u200D\uD83D\uDC67|\uD83D\uDC69(?:\uD83C[\uDFFB-\uDFFF])\u200D(?:\uD83C[\uDF3E\uDF73\uDF93\uDFA4\uDFA8\uDFEB\uDFED]|\uD83D[\uDCBB\uDCBC\uDD27\uDD2C\uDE80\uDE92]|\uD83E[\uDDB0-\uDDB3])|\uD83D\uDC69\u200D\uD83D\uDC66|\uD83C\uDDF6\uD83C\uDDE6|\uD83C\uDDFD\uD83C\uDDF0|\uD83C\uDDF4\uD83C\uDDF2|\uD83D\uDC69(?:\uD83C[\uDFFB-\uDFFF])|\uD83C\uDDED(?:\uD83C[\uDDF0\uDDF2\uDDF3\uDDF7\uDDF9\uDDFA])|\uD83C\uDDEC(?:\uD83C[\uDDE6\uDDE7\uDDE9-\uDDEE\uDDF1-\uDDF3\uDDF5-\uDDFA\uDDFC\uDDFE])|\uD83C\uDDEA(?:\uD83C[\uDDE6\uDDE8\uDDEA\uDDEC\uDDED\uDDF7-\uDDFA])|\uD83C\uDDE8(?:\uD83C[\uDDE6\uDDE8\uDDE9\uDDEB-\uDDEE\uDDF0-\uDDF5\uDDF7\uDDFA-\uDDFF])|\uD83C\uDDF2(?:\uD83C[\uDDE6\uDDE8-\uDDED\uDDF0-\uDDFF])|\uD83C\uDDF3(?:\uD83C[\uDDE6\uDDE8\uDDEA-\uDDEC\uDDEE\uDDF1\uDDF4\uDDF5\uDDF7\uDDFA\uDDFF])|\uD83C\uDDFC(?:\uD83C[\uDDEB\uDDF8])|\uD83C\uDDFA(?:\uD83C[\uDDE6\uDDEC\uDDF2\uDDF3\uDDF8\uDDFE\uDDFF])|\uD83C\uDDF0(?:\uD83C[\uDDEA\uDDEC-\uDDEE\uDDF2\uDDF3\uDDF5\uDDF7\uDDFC\uDDFE\uDDFF])|\uD83C\uDDEF(?:\uD83C[\uDDEA\uDDF2\uDDF4\uDDF5])|\uD83C\uDDF8(?:\uD83C[\uDDE6-\uDDEA\uDDEC-\uDDF4\uDDF7-\uDDF9\uDDFB\uDDFD-\uDDFF])|\uD83C\uDDEE(?:\uD83C[\uDDE8-\uDDEA\uDDF1-\uDDF4\uDDF6-\uDDF9])|\uD83C\uDDFF(?:\uD83C[\uDDE6\uDDF2\uDDFC])|\uD83C\uDDEB(?:\uD83C[\uDDEE-\uDDF0\uDDF2\uDDF4\uDDF7])|\uD83C\uDDF5(?:\uD83C[\uDDE6\uDDEA-\uDDED\uDDF0-\uDDF3\uDDF7-\uDDF9\uDDFC\uDDFE])|\uD83C\uDDE9(?:\uD83C[\uDDEA\uDDEC\uDDEF\uDDF0\uDDF2\uDDF4\uDDFF])|\uD83C\uDDF9(?:\uD83C[\uDDE6\uDDE8\uDDE9\uDDEB-\uDDED\uDDEF-\uDDF4\uDDF7\uDDF9\uDDFB\uDDFC\uDDFF])|\uD83C\uDDE7(?:\uD83C[\uDDE6\uDDE7\uDDE9-\uDDEF\uDDF1-\uDDF4\uDDF6-\uDDF9\uDDFB\uDDFC\uDDFE\uDDFF])|[#\*0-9]\uFE0F\u20E3|\uD83C\uDDF1(?:\uD83C[\uDDE6-\uDDE8\uDDEE\uDDF0\uDDF7-\uDDFB\uDDFE])|\uD83C\uDDE6(?:\uD83C[\uDDE8-\uDDEC\uDDEE\uDDF1\uDDF2\uDDF4\uDDF6-\uDDFA\uDDFC\uDDFD\uDDFF])|\uD83C\uDDF7(?:\uD83C[\uDDEA\uDDF4\uDDF8\uDDFA\uDDFC])|\uD83C\uDDFB(?:\uD83C[\uDDE6\uDDE8\uDDEA\uDDEC\uDDEE\uDDF3\uDDFA])|\uD83C\uDDFE(?:\uD83C[\uDDEA\uDDF9])|(?:\uD83C[\uDFC3\uDFC4\uDFCA]|\uD83D[\uDC6E\uDC71\uDC73\uDC77\uDC81\uDC82\uDC86\uDC87\uDE45-\uDE47\uDE4B\uDE4D\uDE4E\uDEA3\uDEB4-\uDEB6]|\uD83E[\uDD26\uDD37-\uDD39\uDD3D\uDD3E\uDDB8\uDDB9\uDDD6-\uDDDD])(?:\uD83C[\uDFFB-\uDFFF])|(?:\u26F9|\uD83C[\uDFCB\uDFCC]|\uD83D\uDD75)(?:\uD83C[\uDFFB-\uDFFF])|(?:[\u261D\u270A-\u270D]|\uD83C[\uDF85\uDFC2\uDFC7]|\uD83D[\uDC42\uDC43\uDC46-\uDC50\uDC66\uDC67\uDC70\uDC72\uDC74-\uDC76\uDC78\uDC7C\uDC83\uDC85\uDCAA\uDD74\uDD7A\uDD90\uDD95\uDD96\uDE4C\uDE4F\uDEC0\uDECC]|\uD83E[\uDD18-\uDD1C\uDD1E\uDD1F\uDD30-\uDD36\uDDB5\uDDB6\uDDD1-\uDDD5])(?:\uD83C[\uDFFB-\uDFFF])|(?:[\u231A\u231B\u23E9-\u23EC\u23F0\u23F3\u25FD\u25FE\u2614\u2615\u2648-\u2653\u267F\u2693\u26A1\u26AA\u26AB\u26BD\u26BE\u26C4\u26C5\u26CE\u26D4\u26EA\u26F2\u26F3\u26F5\u26FA\u26FD\u2705\u270A\u270B\u2728\u274C\u274E\u2753-\u2755\u2757\u2795-\u2797\u27B0\u27BF\u2B1B\u2B1C\u2B50\u2B55]|\uD83C[\uDC04\uDCCF\uDD8E\uDD91-\uDD9A\uDDE6-\uDDFF\uDE01\uDE1A\uDE2F\uDE32-\uDE36\uDE38-\uDE3A\uDE50\uDE51\uDF00-\uDF20\uDF2D-\uDF35\uDF37-\uDF7C\uDF7E-\uDF93\uDFA0-\uDFCA\uDFCF-\uDFD3\uDFE0-\uDFF0\uDFF4\uDFF8-\uDFFF]|\uD83D[\uDC00-\uDC3E\uDC40\uDC42-\uDCFC\uDCFF-\uDD3D\uDD4B-\uDD4E\uDD50-\uDD67\uDD7A\uDD95\uDD96\uDDA4\uDDFB-\uDE4F\uDE80-\uDEC5\uDECC\uDED0-\uDED2\uDEEB\uDEEC\uDEF4-\uDEF9]|\uD83E[\uDD10-\uDD3A\uDD3C-\uDD3E\uDD40-\uDD45\uDD47-\uDD70\uDD73-\uDD76\uDD7A\uDD7C-\uDDA2\uDDB0-\uDDB9\uDDC0-\uDDC2\uDDD0-\uDDFF])|(?:[#\*0-9\xA9\xAE\u203C\u2049\u2122\u2139\u2194-\u2199\u21A9\u21AA\u231A\u231B\u2328\u23CF\u23E9-\u23F3\u23F8-\u23FA\u24C2\u25AA\u25AB\u25B6\u25C0\u25FB-\u25FE\u2600-\u2604\u260E\u2611\u2614\u2615\u2618\u261D\u2620\u2622\u2623\u2626\u262A\u262E\u262F\u2638-\u263A\u2640\u2642\u2648-\u2653\u265F\u2660\u2663\u2665\u2666\u2668\u267B\u267E\u267F\u2692-\u2697\u2699\u269B\u269C\u26A0\u26A1\u26AA\u26AB\u26B0\u26B1\u26BD\u26BE\u26C4\u26C5\u26C8\u26CE\u26CF\u26D1\u26D3\u26D4\u26E9\u26EA\u26F0-\u26F5\u26F7-\u26FA\u26FD\u2702\u2705\u2708-\u270D\u270F\u2712\u2714\u2716\u271D\u2721\u2728\u2733\u2734\u2744\u2747\u274C\u274E\u2753-\u2755\u2757\u2763\u2764\u2795-\u2797\u27A1\u27B0\u27BF\u2934\u2935\u2B05-\u2B07\u2B1B\u2B1C\u2B50\u2B55\u3030\u303D\u3297\u3299]|\uD83C[\uDC04\uDCCF\uDD70\uDD71\uDD7E\uDD7F\uDD8E\uDD91-\uDD9A\uDDE6-\uDDFF\uDE01\uDE02\uDE1A\uDE2F\uDE32-\uDE3A\uDE50\uDE51\uDF00-\uDF21\uDF24-\uDF93\uDF96\uDF97\uDF99-\uDF9B\uDF9E-\uDFF0\uDFF3-\uDFF5\uDFF7-\uDFFF]|\uD83D[\uDC00-\uDCFD\uDCFF-\uDD3D\uDD49-\uDD4E\uDD50-\uDD67\uDD6F\uDD70\uDD73-\uDD7A\uDD87\uDD8A-\uDD8D\uDD90\uDD95\uDD96\uDDA4\uDDA5\uDDA8\uDDB1\uDDB2\uDDBC\uDDC2-\uDDC4\uDDD1-\uDDD3\uDDDC-\uDDDE\uDDE1\uDDE3\uDDE8\uDDEF\uDDF3\uDDFA-\uDE4F\uDE80-\uDEC5\uDECB-\uDED2\uDEE0-\uDEE5\uDEE9\uDEEB\uDEEC\uDEF0\uDEF3-\uDEF9]|\uD83E[\uDD10-\uDD3A\uDD3C-\uDD3E\uDD40-\uDD45\uDD47-\uDD70\uDD73-\uDD76\uDD7A\uDD7C-\uDDA2\uDDB0-\uDDB9\uDDC0-\uDDC2\uDDD0-\uDDFF])\uFE0F|(?:[\u261D\u26F9\u270A-\u270D]|\uD83C[\uDF85\uDFC2-\uDFC4\uDFC7\uDFCA-\uDFCC]|\uD83D[\uDC42\uDC43\uDC46-\uDC50\uDC66-\uDC69\uDC6E\uDC70-\uDC78\uDC7C\uDC81-\uDC83\uDC85-\uDC87\uDCAA\uDD74\uDD75\uDD7A\uDD90\uDD95\uDD96\uDE45-\uDE47\uDE4B-\uDE4F\uDEA3\uDEB4-\uDEB6\uDEC0\uDECC]|\uD83E[\uDD18-\uDD1C\uDD1E\uDD1F\uDD26\uDD30-\uDD39\uDD3D\uDD3E\uDDB5\uDDB6\uDDB8\uDDB9\uDDD1-\uDDDD])/g;
        const maxLines = 5;
        const maxLineLength = 32;
        const maxCharCount = 160;
        var lastValLength;

        if(shouldShowPopup && !getCookie('autopopup') && (window.checkoutConfig.quoteItemData.length >= 1)){
            openPopup();
        }

        $('.magnifier').on('click', function(){
            previewOverlay.fadeIn().promise().done(function(){
                previewOverlay.addClass('faded')
            });
        });

        $(document).on('click', '.faded', function(){
            $(this).removeClass('faded');
            $(this).fadeOut();
        });

        function openPopup(){
            let cookie = getCookie('autopopup');
            if(!cookie){
                setCookie('autopopup', 1)
            }
            uncheckBox($('.hascard input'));
            $('.giftaddtocart').attr('disabled', false);
            if(window.outerWidth > 768){
                popup.parent().fadeIn();
            }else{
                if(!popup.hasClass('edit')) {
                    $('#giftsection').show();
                }
                var translate = popup.hasClass('edit') ? "translate(0px, 25%)" : "translate(0, 0)";
                if (popup.hasClass('edit') && window.outerWidth <= 375) { // support for screens < 375 resolution iphone 6/7/8
                    translate = "translate(0px, 12%)";
                }
                if (popup.hasClass('edit') && window.outerWidth <= 320) { // support for screens < 320px resolution iphone 5 below
                    translate = "translate(0px, 5%)";
                }
                if(isIos()) document.body.style.position = 'fixed';
                overlay.show("slide", { direction: "right" }, 500);
                popup.css('transform', translate);
            }
        }

        function addToCart(){
            let formData = new FormData(), request = [], cardObject = {};
            if(isMobile() && !popup.hasClass('edit')) {
                textarea = $('#giftsection #cardtext');
            }
            let cardText = textarea.val();
            let url = textarea.data('url');
            let edit = popup.data('item-id');
            cardObject['sku'] = textarea.data('sku');

            let loader = $('#giftloader');
            loader.show();

            if(edit){
                cardObject['item_id'] = edit;
                popup.removeData('item-id');
            }

            if(jQuery('.hascard input').is(':checked')) {
                cardObject['text'] = cardText.split('\n');
                cardObject['type'] = 'note';
                request.push(cardObject);
            }

            if(!$('#nowrapping input').is(':checked')){
                let checked = $('.wrap input:checked');
                for(let i = 0; i < checked.length; i++){
                    let _this = $(checked[i]);
                    let wrapObject = {};
                    wrapObject['sku'] = _this.data('sku');
                    wrapObject['type'] = _this.data('type');
                    request.push(wrapObject);
                }
            }

            if(request.length != 0) {
                request = sanitizeFormData(request);
                formData.append('request_data',JSON.stringify(request));
                $.ajax({
                    type: 'POST',
                    url: url,
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false
                }).done(function (response) {
                    location.reload();
                    $('.giftaddtocart').attr('disabled', false);
                }).fail(function (response) {
                    location.reload();
                    $('.giftaddtocart').attr('disabled', false);
                })
            }else{
                loader.hide();
                closePopup();
            }
        }

        function openEditPopup(quote_item_id, json){
            let values = JSON.parse(atob(json));
            let text = '';

            for(let i=0; i < values.length; i++){
                if(i+1 != values.length){
                    text += values[i]+'\n';
                }else{
                    text += values[i];
                }
            }

            $('#giftsection').hide();
            popup.addClass('edit');
            // popup.data('item-id', quote_item_id);
            popup.attr('data-item-id', quote_item_id);

            if(isMobile() && popup.hasClass('edit')) {
                textarea = $('#cardsection #cardtext');
            }
            textarea.val(text);
            // uncheckBox($('#nocard input'));
            openPopup();
            checkBox(jQuery('.hascard input'));
        }
        window.openEditPopup = openEditPopup;

        function isIos() {
            return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

        }

        function setCookie(name,value,days) {
            let expires = "";
            if (days) {
                let date = new Date();
                date.setTime(date.getTime() + (days*24*60*60*1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "")  + expires + "; path=/";
        }

        function getCookie(name) {
            let nameEQ = name + "=";
            let ca = document.cookie.split(';');
            for(let i=0;i < ca.length;i++) {
                let c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        }

        function closePopup() {
            if(popup.hasClass('edit') && window.outerWidth > 768){
                overlay.fadeOut().promise().done(function(){
                    $('#giftsection').show();
                    // popup.removeClass('edit');
                });
                return;
            }
            if(window.outerWidth > 768){
                // popup.parent().fadeOut();
                button.removeClass('pulse');
                let pos = button[0].getBoundingClientRect();
                overlay.css({
                    left: pos.left + button.width()/2,
                    top: pos.top
                });
                popup.removeClass('edit');
                overlay.addClass('fading');
                // SAME TIME AS THE CSS ANIMATION
                setTimeout(function(){
                    button.addClass('pulse');
                    overlay.css('display', 'none');
                    overlay.removeClass('fading');
                    overlay.css({'left':0, 'top': 0});
                }, 500)
            }else{
                var translate = popup.hasClass('edit') ? "translate(110%, 25%)" : "translate(110%, 0)";
                if(isIos()) document.body.style.position = 'relative';
                overlay.hide("slide", { direction: "right" }, 500);
                popup.css('transform', translate);
                setTimeout(function(){
                    popup.removeClass('edit');
                }, 500);
            }
        }

        // $(document).on('keypress', '#cardtext', function(e){
        $(document).on('keyup', '#cardtext', function(e){
            let value = $(this).val();
            let cursorPos = $(this).prop('selectionEnd');
            if (e.which === 32 && value.length > 1 && value.split("")[value.length-1] == " " && value.split("")[value.length-2] == " ") {
                $(this).val(value.slice(0,value.length-1));
                value = value.slice(0,value.length-1);
            }
            if(reachedEndCheck(value, cursorPos)){
                e.preventDefault();
                if(lastValLength && lastValLength < value.length){
                    $(this).val(value.slice(0,value.length -1));
                }
                lastValLength = $(this).val().length;
                return false;
            }
            if($(this).val().length == 0){
                // checkBox($('#nocard input'));
                uncheckBox(jQuery('.hascard input'));
            }else{
                // uncheckBox($('#nocard input'));
                checkBox(jQuery('.hascard input'));
            }
            // Check the length of all the lines in the textbox
            let lines = checkLineLength(value);
            $(this).val(lines.goodLines+createNewLines(lines.toSplitLines));
            lastCursorPos = cursorPos;
            $(this).prop('selectionEnd', cursorPos);
        }).on('keydown', '#cardtext', function(e){
            if((e.which < 32 || e.which == 46) && e.which != 13){
                $(this).trigger('keypress');
            }
        });

        $(document).on('input paste', '#cardtext', function(){
            let value = $(this).val().replace(emojiRegex, '');
            $(this).val(value);
            checkConditions(value);
        });

        function reachedEndCheck(value, cursorPosition) {
            let lines = value.split('\n');
            if(lines.length >= 5){
                if(lines[maxLines -1].length >= maxLineLength && (cursorPosition == value.length)){
                    return true;
                }
            }else{
                return false;
            }
        }

        function checkLineLength(value) {
            let valueToSplit = [], correctLines = [], alreadySplitted = "", longLineFound = false, lastCharNewline = false;

            // Split the value of the textarea on the newlines to check if any lines are longer than 33 chars
            let lines = value.split('\n');

            // Remove sixth line
            if(lines.length > maxLines){
                lines.length = maxLines;
            }

            for(let i = 0;i < lines.length; i++){
                // check to see if the line has spaces and can be splitted
                // else add a space at the maxlinelength
                if(lines[i].length >= maxLineLength && lines[i].split(' ').length == 1){
                    lines[i] = lines[i].slice(0, maxLineLength) +" "
                }
                if(longLineFound){
                    valueToSplit.push(lines[i]);
                    continue;
                }

                //if bigger than 33, add all the following to the line and set that line as the line to be splitted
                if(lines[i].length > maxLineLength && !longLineFound){
                    longLineFound = true;
                    valueToSplit.push(lines[i]);
                    correctLines[i - 1] += "\n";
                }else{
                    correctLines.push(lines[i]);
                }
            }
            // Reset the earlier removed newLines
            alreadySplitted = correctLines.join('\n');

            return {
                goodLines : alreadySplitted,
                toSplitLines : valueToSplit.join(' ')
            }
        }

        function checkConditions(value) {
            let lines = value.split('\n');
            lines.filter(function(line){
                if(line.length > 33){
                    textarea.trigger('keypress');
                }
            });
            let linesAfterFirstCheck = textarea.val().split('\n');
            if(linesAfterFirstCheck.length > 4){
                if(linesAfterFirstCheck[4].length > maxLineLength){
                    linesAfterFirstCheck[4] = linesAfterFirstCheck[4].slice(0, maxLineLength);
                    textarea.val(linesAfterFirstCheck.join('\n'));
                }
            }
            if(linesAfterFirstCheck.length > maxLines){
                linesAfterFirstCheck.length = maxLines;
                textarea.val(linesAfterFirstCheck.join('\n'));
                textarea.prop('selectionEnd', lastCursorPos);
            }
        }

        function createNewLines(value){
            let newValue = "", lineBreaksNeeded = [];
            const indexesSpaces = getIndexSpaces(value);
            // Loop over array of indexes to find the one closest to the max line length or a multiplier for that if someone copies a text
            for(let i = 1; i < Math.ceil(value.length/maxLineLength); i++){
                let closest = indexesSpaces.reduce(function(prev, curr) {
                    // get the last linebreak with minus 2 to compensate for the index
                    // the second modulo is needed to convert 30 -> 0
                    // Check if the last index was less than 30 and compensate for that linebreak on the next line
                    let lastIndexCorrection = lineBreaksNeeded[i - 2] < maxLineLength * (i - 1) ? maxLineLength - lineBreaksNeeded[i - 2] % maxLineLength : 0;
                    return ((Math.abs(curr - (maxLineLength * i - lastIndexCorrection)) < Math.abs(prev - (maxLineLength * i - lastIndexCorrection))
                        // Current can not be more than maxLineLength + 3
                        && curr - maxLineLength * i < 4)
                    // Check if the prev is already used
                    || lineBreaksNeeded.indexOf(prev) > -1
                        ? curr : prev);
                });
                lineBreaksNeeded.push(closest);
            }
            if(!lineBreaksNeeded.length){
                return newValue += value;
            }else{
                for(let j = 0; j < lineBreaksNeeded.length; j++){
                    // the first -1 is to get the previous linebreakneeded and the second +1 is to compensate for the
                    // white space that gets replaces by a newline
                    newValue += value.slice(lineBreaksNeeded[j-1]+1,lineBreaksNeeded[j])+'\n';
                    if(j == lineBreaksNeeded.length-1){
                        // Make sure the last line is never longer than the maxlinelength (check for last line if either splitted textarea is 5 or if
                        let remove = value.length - (lineBreaksNeeded[j]+1) > maxLineLength && (textarea.val().split('\n').length == 5 || newValue.split('\n').length == 5) ? value.length - (lineBreaksNeeded[j]+1) - maxLineLength : 0;
                        newValue += value.slice(lineBreaksNeeded[j]+1, value.length - remove);
                    }
                }
                return newValue;
            }
        }

        function getIndexSpaces(value){
            let indexesSpaces = [];
            for(let i = 0; i < value.length; i++){
                if(value[i] === " ") indexesSpaces.push(i);
            }
            return indexesSpaces;
        }

        function checkBox(element){
            element.prop('checked', true);
        }

        function uncheckBox(element){
            element.prop('checked', false);
        }

        textarea.on('paste', function(e){
            let pasteData = e.originalEvent.clipboardData.getData('text');
            let noLineBreaks = pasteData.split('\n').join(' ');
            let allowedValue = noLineBreaks.substring(0,maxCharCount);
            $(this).val(allowedValue);
            $(this).trigger('keypress');
            e.preventDefault();
        });

        $(document).on('click', '#addcard', function(){
            let data = {};
            data.sku = $(this).data('sku');

        });

        $(document).on('click', '.giftaddtocart', function(){
            $(this).attr('disabled', true);
            addToCart();
        });

        $(document).on('click','.wrap input', function(){
            let data = $(this).data();
            updateText(data);
            uncheckBox($('#nowrapping input'));
        });

        $(document).on('click', '#nowrapping', function(){
            $('.wrap input').prop('checked', false);
            let data = $('.gifttextholder').data();
            updateText(data);

        });

        $(document).on('click', '.giftoverlay', function(e){
            if($(e.target).hasClass('giftoverlay')){
                closePopup();
            }
        });

        function updateText(data){
            $('.gifttextholder').fadeOut('fast').promise().done(function(){
                $('.gifttextholder h3').text(data.title);
                $('.gifttextholder p').text(data.description);
                $('.wrapprice').text(data.price);
                $('.gifttextholder').fadeIn('fast');
            })
        }

        function isMobile() {
            var isMobile = false;
            if (Math.max($(window).width(), window.innerWidth) <= 768) {
                isMobile = true;
            }
            return isMobile;
        }

        function sanitizeFormData(formData) {
            var formData = formData.reduce((_filtered, item) => {
                if ( !_filtered.includes(item.sku) && item.type !== undefined ) {
                    _filtered.push(item);
                }
                return _filtered;
            }, []);
            return formData;
        }

        return function(config, element) {
            $(".giftpopupbutton").click(function(e){
                e.preventDefault();
                textarea.val('');
                $("#giftpopup").show();
                popup.removeData('item-id');
                openPopup();
            });

            $(".kruisje").click(function(){
                closePopup();
            });
        }
    }
)
