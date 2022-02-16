define([
    'jquery',
    'jquery-ui-modules/widget',
    'mage/translate'
], function($) {
    "use strict";
    $.widget('mage.descriptionToolbar', {
        options: {
            seeMoreLabel: $.mage.__('SEE MORE'),
            seeLessLabel: $.mage.__('SEE LESS'),
            paragraphHeight: 35
        },

        _create: function() {
            this._moveFiltersBlock();
            this._catSeeMore();
        },

        _hideTextMobile: function () {
            var $elementToShorten = $('.category-description p')[0];

            if ($elementToShorten) {
                var currentParaHeight = $elementToShorten.scrollHeight;

                if (currentParaHeight > this.options.paragraphHeight && window.innerWidth < 769 && !($('.catSeeMore').length)) {
                    var $showMore = $('<p/>', {
                        "class": 'catSeeMore'
                    });

                    $('.column.main .category-description').first().append($showMore);
                    $showMore.text(this.options.seeMoreLabel);
                }
            }
        },

        _catSeeMore: function () {
            this._hideTextMobile();

            var options = this.options;

            $(document).on('click', '.catSeeMore', function () {
                var self = $(this),
                    $descriptionParagraph = $('.category-description p'),
                    height = $descriptionParagraph[0].scrollHeight;

                if (!self.hasClass('expanded')) {
                    $descriptionParagraph.animate({
                        'maxHeight': height
                    }).promise().done(function () {
                        self.text(options.seeLessLabel).addClass('expanded');
                    });
                } else {
                    $descriptionParagraph.animate({
                        'maxHeight': options.paragraphHeight + "px"
                    }).promise().done(function () {
                        self.text(options.seeMoreLabel).removeClass('expanded');
                        var sticky = $('.block.filter'),
                            scroll = $(window).scrollTop(),
                            scrollLimit = 300; // correct offset height if on category search result page
                        if(scrollLimit >= scroll){
                            sticky.addClass('fixed');
                            sticky.css('width', 'auto');

                            if(sticky.hasClass('fixed')) {
                                $('#layered-filter-block').css("top", parseFloat($('.header-m-primary-container').height()) + 'px');
                            }
                        }
                    });
                }
            });
        },

        _moveFiltersBlock: function () {
            var $filtersBlock = $('#layered-filter-block'),
                descriptionBlock = '.category-description';

            if (!$filtersBlock.length && !($(descriptionBlock).length)) {
                return false;
            }

            if (window.innerWidth < 768) {
                $filtersBlock.insertAfter($(descriptionBlock)[0]);
            }
        }
    });

    return $.mage.descriptionToolbar;
});
