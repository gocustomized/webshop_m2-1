define([
    'jquery',
    'jquery-ui-modules/widget'
], function($) {
    "use strict";

    /**
     * Performance improve :
     *
     * Somehow click event runs 7 times after one click, added unbind method to run it only once.
     */
    $.widget('mage.faq_collapse', {
        _create: function() {
            $('.faqholder, .faq').unbind().on('click', function() {
                $(this).siblings().removeClass('expanded');
                $(this).toggleClass('expanded');
            });

            var sidetopics = '.column.main > .block-faqs .sidetopics',
                current = $('.sidetopics .current');

            if (current.index() !== 0) {
                $(sidetopics).find('ul').prepend(current);
            }

            $(document).unbind().on('click', sidetopics, function() {
                if (window.outerWidth < 767 && !$(sidetopics).hasClass('expanded')) {
                    $(sidetopics).toggleClass('expanded');
                }
            });

            $('body').unbind().on('click', function(e) {
                if ($(e.target).closest(sidetopics).length === 0) {
                    $(sidetopics).removeClass('expanded');
                }
            });
        }
    });

    return $.mage.faq_collapse;
});
