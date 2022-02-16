// Pop-up for the gift wrapping section
//

const maxLineLength = 30;
const maxLines = 5;
const maxCharCount = 150;
const emojiRegex = /\uD83C\uDFF4(?:\uDB40\uDC67\uDB40\uDC62(?:\uDB40\uDC65\uDB40\uDC6E\uDB40\uDC67|\uDB40\uDC77\uDB40\uDC6C\uDB40\uDC73|\uDB40\uDC73\uDB40\uDC63\uDB40\uDC74)\uDB40\uDC7F|\u200D\u2620\uFE0F)|\uD83D\uDC69\u200D\uD83D\uDC69\u200D(?:\uD83D\uDC66\u200D\uD83D\uDC66|\uD83D\uDC67\u200D(?:\uD83D[\uDC66\uDC67]))|\uD83D\uDC68(?:\u200D(?:\u2764\uFE0F\u200D(?:\uD83D\uDC8B\u200D)?\uD83D\uDC68|(?:\uD83D[\uDC68\uDC69])\u200D(?:\uD83D\uDC66\u200D\uD83D\uDC66|\uD83D\uDC67\u200D(?:\uD83D[\uDC66\uDC67]))|\uD83D\uDC66\u200D\uD83D\uDC66|\uD83D\uDC67\u200D(?:\uD83D[\uDC66\uDC67])|\uD83C[\uDF3E\uDF73\uDF93\uDFA4\uDFA8\uDFEB\uDFED]|\uD83D[\uDCBB\uDCBC\uDD27\uDD2C\uDE80\uDE92]|\uD83E[\uDDB0-\uDDB3])|(?:\uD83C[\uDFFB-\uDFFF])\u200D(?:\uD83C[\uDF3E\uDF73\uDF93\uDFA4\uDFA8\uDFEB\uDFED]|\uD83D[\uDCBB\uDCBC\uDD27\uDD2C\uDE80\uDE92]|\uD83E[\uDDB0-\uDDB3]))|\uD83D\uDC69\u200D(?:\u2764\uFE0F\u200D(?:\uD83D\uDC8B\u200D(?:\uD83D[\uDC68\uDC69])|\uD83D[\uDC68\uDC69])|\uD83C[\uDF3E\uDF73\uDF93\uDFA4\uDFA8\uDFEB\uDFED]|\uD83D[\uDCBB\uDCBC\uDD27\uDD2C\uDE80\uDE92]|\uD83E[\uDDB0-\uDDB3])|\uD83D\uDC69\u200D\uD83D\uDC66\u200D\uD83D\uDC66|(?:\uD83D\uDC41\uFE0F\u200D\uD83D\uDDE8|\uD83D\uDC69(?:\uD83C[\uDFFB-\uDFFF])\u200D[\u2695\u2696\u2708]|\uD83D\uDC68(?:(?:\uD83C[\uDFFB-\uDFFF])\u200D[\u2695\u2696\u2708]|\u200D[\u2695\u2696\u2708])|(?:(?:\u26F9|\uD83C[\uDFCB\uDFCC]|\uD83D\uDD75)\uFE0F|\uD83D\uDC6F|\uD83E[\uDD3C\uDDDE\uDDDF])\u200D[\u2640\u2642]|(?:\u26F9|\uD83C[\uDFCB\uDFCC]|\uD83D\uDD75)(?:\uD83C[\uDFFB-\uDFFF])\u200D[\u2640\u2642]|(?:\uD83C[\uDFC3\uDFC4\uDFCA]|\uD83D[\uDC6E\uDC71\uDC73\uDC77\uDC81\uDC82\uDC86\uDC87\uDE45-\uDE47\uDE4B\uDE4D\uDE4E\uDEA3\uDEB4-\uDEB6]|\uD83E[\uDD26\uDD37-\uDD39\uDD3D\uDD3E\uDDB8\uDDB9\uDDD6-\uDDDD])(?:(?:\uD83C[\uDFFB-\uDFFF])\u200D[\u2640\u2642]|\u200D[\u2640\u2642])|\uD83D\uDC69\u200D[\u2695\u2696\u2708])\uFE0F|\uD83D\uDC69\u200D\uD83D\uDC67\u200D(?:\uD83D[\uDC66\uDC67])|\uD83D\uDC69\u200D\uD83D\uDC69\u200D(?:\uD83D[\uDC66\uDC67])|\uD83D\uDC68(?:\u200D(?:(?:\uD83D[\uDC68\uDC69])\u200D(?:\uD83D[\uDC66\uDC67])|\uD83D[\uDC66\uDC67])|\uD83C[\uDFFB-\uDFFF])|\uD83C\uDFF3\uFE0F\u200D\uD83C\uDF08|\uD83D\uDC69\u200D\uD83D\uDC67|\uD83D\uDC69(?:\uD83C[\uDFFB-\uDFFF])\u200D(?:\uD83C[\uDF3E\uDF73\uDF93\uDFA4\uDFA8\uDFEB\uDFED]|\uD83D[\uDCBB\uDCBC\uDD27\uDD2C\uDE80\uDE92]|\uD83E[\uDDB0-\uDDB3])|\uD83D\uDC69\u200D\uD83D\uDC66|\uD83C\uDDF6\uD83C\uDDE6|\uD83C\uDDFD\uD83C\uDDF0|\uD83C\uDDF4\uD83C\uDDF2|\uD83D\uDC69(?:\uD83C[\uDFFB-\uDFFF])|\uD83C\uDDED(?:\uD83C[\uDDF0\uDDF2\uDDF3\uDDF7\uDDF9\uDDFA])|\uD83C\uDDEC(?:\uD83C[\uDDE6\uDDE7\uDDE9-\uDDEE\uDDF1-\uDDF3\uDDF5-\uDDFA\uDDFC\uDDFE])|\uD83C\uDDEA(?:\uD83C[\uDDE6\uDDE8\uDDEA\uDDEC\uDDED\uDDF7-\uDDFA])|\uD83C\uDDE8(?:\uD83C[\uDDE6\uDDE8\uDDE9\uDDEB-\uDDEE\uDDF0-\uDDF5\uDDF7\uDDFA-\uDDFF])|\uD83C\uDDF2(?:\uD83C[\uDDE6\uDDE8-\uDDED\uDDF0-\uDDFF])|\uD83C\uDDF3(?:\uD83C[\uDDE6\uDDE8\uDDEA-\uDDEC\uDDEE\uDDF1\uDDF4\uDDF5\uDDF7\uDDFA\uDDFF])|\uD83C\uDDFC(?:\uD83C[\uDDEB\uDDF8])|\uD83C\uDDFA(?:\uD83C[\uDDE6\uDDEC\uDDF2\uDDF3\uDDF8\uDDFE\uDDFF])|\uD83C\uDDF0(?:\uD83C[\uDDEA\uDDEC-\uDDEE\uDDF2\uDDF3\uDDF5\uDDF7\uDDFC\uDDFE\uDDFF])|\uD83C\uDDEF(?:\uD83C[\uDDEA\uDDF2\uDDF4\uDDF5])|\uD83C\uDDF8(?:\uD83C[\uDDE6-\uDDEA\uDDEC-\uDDF4\uDDF7-\uDDF9\uDDFB\uDDFD-\uDDFF])|\uD83C\uDDEE(?:\uD83C[\uDDE8-\uDDEA\uDDF1-\uDDF4\uDDF6-\uDDF9])|\uD83C\uDDFF(?:\uD83C[\uDDE6\uDDF2\uDDFC])|\uD83C\uDDEB(?:\uD83C[\uDDEE-\uDDF0\uDDF2\uDDF4\uDDF7])|\uD83C\uDDF5(?:\uD83C[\uDDE6\uDDEA-\uDDED\uDDF0-\uDDF3\uDDF7-\uDDF9\uDDFC\uDDFE])|\uD83C\uDDE9(?:\uD83C[\uDDEA\uDDEC\uDDEF\uDDF0\uDDF2\uDDF4\uDDFF])|\uD83C\uDDF9(?:\uD83C[\uDDE6\uDDE8\uDDE9\uDDEB-\uDDED\uDDEF-\uDDF4\uDDF7\uDDF9\uDDFB\uDDFC\uDDFF])|\uD83C\uDDE7(?:\uD83C[\uDDE6\uDDE7\uDDE9-\uDDEF\uDDF1-\uDDF4\uDDF6-\uDDF9\uDDFB\uDDFC\uDDFE\uDDFF])|[#\*0-9]\uFE0F\u20E3|\uD83C\uDDF1(?:\uD83C[\uDDE6-\uDDE8\uDDEE\uDDF0\uDDF7-\uDDFB\uDDFE])|\uD83C\uDDE6(?:\uD83C[\uDDE8-\uDDEC\uDDEE\uDDF1\uDDF2\uDDF4\uDDF6-\uDDFA\uDDFC\uDDFD\uDDFF])|\uD83C\uDDF7(?:\uD83C[\uDDEA\uDDF4\uDDF8\uDDFA\uDDFC])|\uD83C\uDDFB(?:\uD83C[\uDDE6\uDDE8\uDDEA\uDDEC\uDDEE\uDDF3\uDDFA])|\uD83C\uDDFE(?:\uD83C[\uDDEA\uDDF9])|(?:\uD83C[\uDFC3\uDFC4\uDFCA]|\uD83D[\uDC6E\uDC71\uDC73\uDC77\uDC81\uDC82\uDC86\uDC87\uDE45-\uDE47\uDE4B\uDE4D\uDE4E\uDEA3\uDEB4-\uDEB6]|\uD83E[\uDD26\uDD37-\uDD39\uDD3D\uDD3E\uDDB8\uDDB9\uDDD6-\uDDDD])(?:\uD83C[\uDFFB-\uDFFF])|(?:\u26F9|\uD83C[\uDFCB\uDFCC]|\uD83D\uDD75)(?:\uD83C[\uDFFB-\uDFFF])|(?:[\u261D\u270A-\u270D]|\uD83C[\uDF85\uDFC2\uDFC7]|\uD83D[\uDC42\uDC43\uDC46-\uDC50\uDC66\uDC67\uDC70\uDC72\uDC74-\uDC76\uDC78\uDC7C\uDC83\uDC85\uDCAA\uDD74\uDD7A\uDD90\uDD95\uDD96\uDE4C\uDE4F\uDEC0\uDECC]|\uD83E[\uDD18-\uDD1C\uDD1E\uDD1F\uDD30-\uDD36\uDDB5\uDDB6\uDDD1-\uDDD5])(?:\uD83C[\uDFFB-\uDFFF])|(?:[\u231A\u231B\u23E9-\u23EC\u23F0\u23F3\u25FD\u25FE\u2614\u2615\u2648-\u2653\u267F\u2693\u26A1\u26AA\u26AB\u26BD\u26BE\u26C4\u26C5\u26CE\u26D4\u26EA\u26F2\u26F3\u26F5\u26FA\u26FD\u2705\u270A\u270B\u2728\u274C\u274E\u2753-\u2755\u2757\u2795-\u2797\u27B0\u27BF\u2B1B\u2B1C\u2B50\u2B55]|\uD83C[\uDC04\uDCCF\uDD8E\uDD91-\uDD9A\uDDE6-\uDDFF\uDE01\uDE1A\uDE2F\uDE32-\uDE36\uDE38-\uDE3A\uDE50\uDE51\uDF00-\uDF20\uDF2D-\uDF35\uDF37-\uDF7C\uDF7E-\uDF93\uDFA0-\uDFCA\uDFCF-\uDFD3\uDFE0-\uDFF0\uDFF4\uDFF8-\uDFFF]|\uD83D[\uDC00-\uDC3E\uDC40\uDC42-\uDCFC\uDCFF-\uDD3D\uDD4B-\uDD4E\uDD50-\uDD67\uDD7A\uDD95\uDD96\uDDA4\uDDFB-\uDE4F\uDE80-\uDEC5\uDECC\uDED0-\uDED2\uDEEB\uDEEC\uDEF4-\uDEF9]|\uD83E[\uDD10-\uDD3A\uDD3C-\uDD3E\uDD40-\uDD45\uDD47-\uDD70\uDD73-\uDD76\uDD7A\uDD7C-\uDDA2\uDDB0-\uDDB9\uDDC0-\uDDC2\uDDD0-\uDDFF])|(?:[#\*0-9\xA9\xAE\u203C\u2049\u2122\u2139\u2194-\u2199\u21A9\u21AA\u231A\u231B\u2328\u23CF\u23E9-\u23F3\u23F8-\u23FA\u24C2\u25AA\u25AB\u25B6\u25C0\u25FB-\u25FE\u2600-\u2604\u260E\u2611\u2614\u2615\u2618\u261D\u2620\u2622\u2623\u2626\u262A\u262E\u262F\u2638-\u263A\u2640\u2642\u2648-\u2653\u265F\u2660\u2663\u2665\u2666\u2668\u267B\u267E\u267F\u2692-\u2697\u2699\u269B\u269C\u26A0\u26A1\u26AA\u26AB\u26B0\u26B1\u26BD\u26BE\u26C4\u26C5\u26C8\u26CE\u26CF\u26D1\u26D3\u26D4\u26E9\u26EA\u26F0-\u26F5\u26F7-\u26FA\u26FD\u2702\u2705\u2708-\u270D\u270F\u2712\u2714\u2716\u271D\u2721\u2728\u2733\u2734\u2744\u2747\u274C\u274E\u2753-\u2755\u2757\u2763\u2764\u2795-\u2797\u27A1\u27B0\u27BF\u2934\u2935\u2B05-\u2B07\u2B1B\u2B1C\u2B50\u2B55\u3030\u303D\u3297\u3299]|\uD83C[\uDC04\uDCCF\uDD70\uDD71\uDD7E\uDD7F\uDD8E\uDD91-\uDD9A\uDDE6-\uDDFF\uDE01\uDE02\uDE1A\uDE2F\uDE32-\uDE3A\uDE50\uDE51\uDF00-\uDF21\uDF24-\uDF93\uDF96\uDF97\uDF99-\uDF9B\uDF9E-\uDFF0\uDFF3-\uDFF5\uDFF7-\uDFFF]|\uD83D[\uDC00-\uDCFD\uDCFF-\uDD3D\uDD49-\uDD4E\uDD50-\uDD67\uDD6F\uDD70\uDD73-\uDD7A\uDD87\uDD8A-\uDD8D\uDD90\uDD95\uDD96\uDDA4\uDDA5\uDDA8\uDDB1\uDDB2\uDDBC\uDDC2-\uDDC4\uDDD1-\uDDD3\uDDDC-\uDDDE\uDDE1\uDDE3\uDDE8\uDDEF\uDDF3\uDDFA-\uDE4F\uDE80-\uDEC5\uDECB-\uDED2\uDEE0-\uDEE5\uDEE9\uDEEB\uDEEC\uDEF0\uDEF3-\uDEF9]|\uD83E[\uDD10-\uDD3A\uDD3C-\uDD3E\uDD40-\uDD45\uDD47-\uDD70\uDD73-\uDD76\uDD7A\uDD7C-\uDDA2\uDDB0-\uDDB9\uDDC0-\uDDC2\uDDD0-\uDDFF])\uFE0F|(?:[\u261D\u26F9\u270A-\u270D]|\uD83C[\uDF85\uDFC2-\uDFC4\uDFC7\uDFCA-\uDFCC]|\uD83D[\uDC42\uDC43\uDC46-\uDC50\uDC66-\uDC69\uDC6E\uDC70-\uDC78\uDC7C\uDC81-\uDC83\uDC85-\uDC87\uDCAA\uDD74\uDD75\uDD7A\uDD90\uDD95\uDD96\uDE45-\uDE47\uDE4B-\uDE4F\uDEA3\uDEB4-\uDEB6\uDEC0\uDECC]|\uD83E[\uDD18-\uDD1C\uDD1E\uDD1F\uDD26\uDD30-\uDD39\uDD3D\uDD3E\uDDB5\uDDB6\uDDB8\uDDB9\uDDD1-\uDDDD])/g;
var lastValLength;

jQuery(document).ready(function(){
    const popup = jQuery('#giftpopup');
    const previewOverlay = jQuery('.previewoverlay');
    const overlay = jQuery('.giftoverlay');
    const button = jQuery('.giftpopupbutton');
    const textarea = jQuery('#cardtext');
    // QUICK FIX TO MAKE THE GIFTPOPUP WORK IN THE CART
    const isOldCart = jQuery('body').hasClass('checkout-cart-index')
    var lastCursorPos = 0;
    const shouldShowPopup = popup.data('show');

    if(shouldShowPopup && !getCookie('autopopup')){
        if(window.outerWidth < 768 && jQuery('body').hasClass('checkout-onepage-index')){
            jQuery('#editcart').trigger('click');
        }
        openPopup();
    }

    button.on('click', function(e){
        e.preventDefault();
        textarea.val('');
        jQuery('#giftsection').show();
        popup.removeData('item-id');
        openPopup();
    });

    jQuery('.kruisje img').on('click', function(){
        closePopup();
    });

    jQuery('.previewcard').on('click', function(){
        previewOverlay.fadeIn().promise().done(function(){
            previewOverlay.addClass('faded')
        });
    });

    jQuery(document).on('click', '.faded', function(){
        jQuery(this).removeClass('faded');
        jQuery(this).fadeOut();
    });

    function openPopup(){
        var cookie = getCookie('autopopup');
        if(!cookie){
            setCookie('autopopup', 1)
        }
        jQuery('.giftaddtocart').attr('disabled', false);
        if(window.outerWidth > 768){
            jQuery('body').addClass('giftopen')
            // QUICK FIX TO MAKE THE GIFT POP-UP WORKING IN THE CART
            if(isOldCart){
                jQuery('#header-cart').show().css('visibility', 'hidden');
                popup.parent().css('visibility', 'visible');
            }
            popup.parent().fadeIn();
        }else{
            // QUICK FIX TO MAKE THE GIFT POP-UP WORKING IN THE CART
            if(isOldCart && !jQuery('.mini-cart-heading').hasClass('skip-active')){
                jQuery('.mini-cart-heading').first().trigger('click');
            }else if(isOldCart && !jQuery('#header-cart').hasClass('expanded')){
                jQuery('#editcart').trigger('click');
            }
            // Needed to make the giftpop-up to be on top of the header in the new checkout
            jQuery('#fakeheader').css('transform', 'translateX(-110%)');
            // if(isIos()) document.body.style.position = 'fixed';
            popup.css('transform', 'translateX(0)');
        }
    }
    
    function isIos() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

    }

    function setCookie(name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }
    
    function closePopup() {
        if(popup.hasClass('edit') && window.outerWidth > 768){
            overlay.fadeOut().promise().done(function(){
                popup.removeClass('edit');
            });
            return;
        }
        if(window.outerWidth > 768){
            // popup.parent().fadeOut();

            button.removeClass('pulse');
            var pos = button[0].getBoundingClientRect();
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
                jQuery('body').removeClass('giftopen')
                if(isOldCart){
                    jQuery('#header-cart').hide().css('visibility', 'visible');
                }
            }, 500)
        }else{
            jQuery('#fakeheader').css('transform', 'translateX(0%)');
            if(isOldCart && !jQuery('#header-cart').hasClass('expanded')){
                jQuery('.mini-cart-heading').first().trigger('click');
            }else if(isOldCart && jQuery('.mini-cart-heading.skip-active')){
                jQuery('#cartcloser').trigger('click');
            }
            // if(isIos()) document.body.style.position = 'relative';
            popup.css('transform', 'translateX(110%)');
        }
    }

    jQuery(document).on('keypress', '#cardtext', function(e){
        var value = jQuery(this).val();
        var cursorPos = jQuery(this).prop('selectionEnd');
        if(reachedEndCheck(value, cursorPos)){
            e.preventDefault();
            if(lastValLength && lastValLength < value.length){
                jQuery(this).val(value.slice(0,value.length -1));
            }
            lastValLength = jQuery(this).val().length;
            return false;
        }
        if(jQuery(this).val().length == 0){
            checkBox(jQuery('#nocard input'));
        }else{
            uncheckBox(jQuery('#nocard input'));
        }
        // Check the length of all the lines in the textbox
        var lines = checkLineLength(value);
        jQuery(this).val(lines.goodLines+createNewLines(lines.toSplitLines));
        lastCursorPos = cursorPos;
        jQuery(this).prop('selectionEnd', cursorPos);
    }).on('keydown', '#cardtext', function(e){
        if((e.which < 32 || e.which == 46) && e.which != 13){
            jQuery(this).trigger('keypress');
        }
    });

    jQuery(document).on('input', '#cardtext', function(){
        var value = jQuery(this).val().replace(emojiRegex, '');
        jQuery(this).val(value);
        checkConditions(value);
    });

    function reachedEndCheck(value, cursorPosition) {
        var lines = value.split('\n');
        if(lines.length >= 5){
            if(lines[maxLines -1].length >= maxLineLength && (cursorPosition == value.length)){
                return true;
            }

            // else if(lines[maxLines -1].length >= maxLineLength - 5){
            //     console.log('HOEZO KOM IK NIET MEER HIER??');
            //     jQuery('#cardtext').css('borderColor', 'red');
            //     jQuery('.reachendwarning').css('color', 'red');
            //     console.log(maxLineLength-lines[maxLines -1].length);
            //     var charsLeft = maxLineLength - lines[maxLines -1].length -1;
            //     jQuery('.reachendwarning').text('you have '+charsLeft+' charcter left to type ');
            // }
        }else{
            return false;
        }
    }

    function updateWarning(line){
        var charsLeft = maxLineLength - line.length + 1;
        if(charsLeft > 5){
            textarea.css('borderColor', 'blue');
            jQuery('.reachendwarning').text("");
            return
        }
        // jQuery('.reachendwarning').text('you have '+charsLeft+' left to type');
    }

    function checkLineLength(value) {
        var valueToSplit = [], correctLines = [], alreadySplitted = "", longLineFound = false, lastCharNewline = false;

        // Split the value of the textarea on the newlines to check if any lines are longer than 33 chars
        var lines = value.split('\n');

        // Remove sixth line
        if(lines.length > maxLines){
            lines.length = maxLines;
        }

        for(var i = 0;i < lines.length; i++){
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
            if(lines[i].length > maxLineLength*1.1 && !longLineFound){
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
        var lines = value.split('\n');
        lines.filter(function(line){
            if(line.length > 33){
                textarea.trigger('keypress');
            }
        });
        // can't use the earlier set vars, since they might be changed by the keypress trigger
        var linesAfterFirstCheck = textarea.val().split('\n');
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


    // ik ben wouter en ik heb echt
    // dikke vette koppijn vandaag.
    // Hopelijk gaat eht snel weer wat
    // beter en kan ik alles fixen wat
    // ik graag wil fixen en lukt het

    function createNewLines(value){
        var newValue = "", lineBreaksNeeded = [];
        const indexesSpaces = getIndexSpaces(value);
        // Loop over array of indexes to find the one closest to the max line length or a multiplier for that if someone copies a text
        for(var i = 1; i < Math.ceil(value.length/maxLineLength); i++){
            var closest = indexesSpaces.reduce(function(prev, curr) {
                // get the last linebreak with minus 2 to compensate for the index
                // the second modulo is needed to convert 30 -> 0
                // Check if the last index was less than 30 and compensate for that linebreak on the next line
                var lastIndexCorrection = lineBreaksNeeded[i - 2] < maxLineLength * (i - 1) ? maxLineLength - lineBreaksNeeded[i - 2] % maxLineLength : 0;
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
            for(var j = 0; j < lineBreaksNeeded.length; j++){
                // the first -1 is to get the previous linebreakneeded and the second +1 is to compensate for the
                // white space that gets replaces by a newline
                newValue += value.slice(lineBreaksNeeded[j-1]+1,lineBreaksNeeded[j])+'\n';
                if(j == lineBreaksNeeded.length-1){
                    // Make sure the last line is never longer than the maxlinelength (check for last line if either splitted textarea is 5 or if
                    var remove = value.length - (lineBreaksNeeded[j]+1) > maxLineLength && (textarea.val().split('\n').length == 5 || newValue.split('\n').length == 5) ? value.length - (lineBreaksNeeded[j]+1) - maxLineLength : 0;
                    newValue += value.slice(lineBreaksNeeded[j]+1, value.length - remove);
                }
            }
            return newValue;
        }
    }

    // Returns an array of indexes of spaces of the last line in the textarea
    function getIndexSpaces(value){
        var indexesSpaces = [];
        for(var i = 0; i < value.length; i++){
            if(value[i] === " ") indexesSpaces.push(i);
        }
        return indexesSpaces;
    }

    function openEditPopup(quote_item_id, json){
        var values = JSON.parse(atob(json));
        var text = '';
        for(var i=0; i < values.length; i++){
            if(i+1 != values.length){
                text += values[i]+'\n';
            }else{
                text += values[i];
            }
        }
        jQuery('#giftsection').hide();
        popup.addClass('edit');
        textarea.val(text);
        uncheckBox(jQuery('#nocard input'));
        popup.data('item-id', quote_item_id);
        openPopup();
    }

    // Expose the function to the global scope, so php can use it
    window.openEditPopup = openEditPopup;

    function checkBox(element){
        element.prop('checked', true);
    }

    function uncheckBox(element){
        element.prop('checked', false);
    }

    //
    textarea.on('paste', function(e){
        var pasteData = e.originalEvent.clipboardData.getData('text');
        var noLineBreaks = pasteData.split('\n').join(' ');
        // var a = noLineBreaks.splice(0,150);
        var allowedValue = noLineBreaks.substring(0,maxCharCount);
        jQuery(this).val(allowedValue);
        jQuery(this).trigger('keypress');
        e.preventDefault();
    });


    jQuery(document).on('click', '#addcard', function(){
        var data = {};
        data.sku = jQuery(this).data('sku');

    });

    jQuery(document).on('click', '.giftaddtocart', function(){
        jQuery(this).attr('disabled', true);
        addToCart();
    });

    jQuery(document).on('click','.wrap input', function(){
        var data = jQuery(this).data();
        updateText(data);
        uncheckBox(jQuery('#nowrapping input'));
    });

    // jQuery(document).on('click', '#nocard', function(){
    //     // CHECK IF THIS IS WHAT WE WANT!!!!!!!!!!
    //     if(jQuery('#nocard input').is(':checked')){
    //         jQuery('#cardtext').css('color', 'white');
    //     }else{
    //         jQuery('#cardtext').css('color', 'black');
    //     }
    // });

    jQuery(document).on('click', '#nowrapping', function(){
        jQuery('.wrap input').prop('checked', false);
        var data = jQuery('.gifttextholder').data();
        updateText(data);

    });

    jQuery(document).on('click', '.giftoverlay', function(e){
        if(jQuery(e.target).hasClass('giftoverlay')){
            // jQuery(this).fadeOut('fast');
            closePopup();
        }
    });

    function updateText(data){
        jQuery('.gifttextholder').fadeOut('fast').promise().done(function(){
            jQuery('.gifttextholder h3').text(data.title);
            jQuery('.gifttextholder p').text(data.description);
            jQuery('.wrapprice').text(data.price);
            jQuery('.gifttextholder').fadeIn('fast');
        })
    }

    function addToCart(){
        var formData = new FormData(), request = [], cardObject = {}, wrapInfo = jQuery('');
        var cardText = textarea.val();
        var loader = jQuery('#giftloader');
        loader.show();
        var url =  textarea.data('url');
        cardObject['sku'] = textarea.data('sku');
        var edit = popup.data('item-id');
        if(edit){
            cardObject['item_id'] = edit;
            popup.removeData('item-id');
        }

        if(cardText.length > 0 && !jQuery('#nocard input').is(':checked')) {
            cardObject['text'] = cardText.split('\n');
            cardObject['type'] = 'note';
            request.push(cardObject);
        }

        if(!jQuery('#nowrapping input').is(':checked')){
            var checked = jQuery('.wrap input:checked');
            for(var i = 0; i < checked.length; i++){
                var _this = jQuery(checked[i]);
                var wrapObject = {};
                wrapObject['sku'] = _this.data('sku');
                wrapObject['type'] = _this.data('type');
                request.push(wrapObject);
            }
        }

        if(request.length != 0) {
            formData.append('request_data',JSON.stringify(request));
            jQuery.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                cache: false
            }).done(function (response) {
                location.reload();
                jQuery('.giftaddtocart').attr('disabled', false);
            }).fail(function (response) {
                location.reload();
                jQuery('.giftaddtocart').attr('disabled', false);
            })
        }else{
            loader.hide();
            closePopup();
        }
    }
});