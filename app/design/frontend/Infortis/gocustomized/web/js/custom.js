require([
    "jquery"
  ],
  function($) {
    "use strict";

    $(document).ready(function() {
        $(".filter-options-title").click(function(){
            var $this = this;
            $(this).next('.filter-options-content').css('transform', 'translateX(0)');
            $('.block.filter').toggleClass('accordion-open');
        });

        $(".close-button").click(function(){
            $('.filter-options-content').css('transform', 'translateX(100%)');
            $('.block.filter').toggleClass('accordion-open');
        });

        $("#sortcloser").click(function(){
            $('html').css('overflow', '');
            $('#sortlist').hide();
            $('.block.filter').toggleClass('active');
            $('body').removeClass('sorter-open');

            if (!$(".block.filter").hasClass('active')) {
                if ($(".block.filter").hasClass('fixed')) {
                    $('.header-container3').show();
                }
            } else {
                $('.header-container3').show();
                $(".block.filter").removeClass('fixed');
                $('#layered-filter-block').css("top", "");
                $(".block.filter").css('width', '100vw');
            }
        });

        $(".sort-by-mobile").click(function(){
            $('html').css('overflow', 'hidden');
            $('#sortlist').toggle();
            $('.block.filter').toggleClass('active');
            $('body').addClass('sorter-open');

            if ($(".block.filter").hasClass('fixed')) {
                $('.header-container3').hide();
            }
        });

        $("#filtercloser").click(function(){

            if ($(".block.filter").hasClass('active')) {
                $('html').css('overflow', 'hidden');
                $('.header-container3').hide();
            } else {
                $('html').css('overflow', '');
                $('.header-container3').show();
            }

            if (!$(".block.filter").hasClass('fixed')) {
                $('.header-container3').show();
                $(".block.filter").removeClass('fixed');
                $('#layered-filter-block').css("top", "");
                $(".block.filter").css('width', '100vw');
            } else {
                if ($(".block.filter").hasClass('active')) {
                    $('.header-container3').hide();
                } else {
                    $('.header-container3').show();

                    var scroll = $(window).scrollTop(),
                        scrollLimit = $('body').hasClass('catalogsearch-result-index') ? 300 : 129;
                        // correct offset height if on category  page 129
                        // correct offset height if on category search result page 300
                        // category description block normal mobile height 117px

                    scrollLimit = $('.catSeeMore').hasClass('expanded') ? (scrollLimit - 117) + $('.category-description').height() : scrollLimit;
                    if(scroll >= scrollLimit){
                        $(".block.filter").addClass('fixed');
                        $('#layered-filter-block').css("top", parseFloat($('.header-m-primary-container').height()) + 'px');
                        $(".block.filter").css('width', 'auto');
                    } else {
                        $(".block.filter").removeClass('fixed');
                        $('#layered-filter-block').css("top", "");
                        $(".block.filter").css('width', '100vw');
                    }
                }
            }
        });

        $(window).scroll(function(){
            var sticky = $('.block.filter'),
                scroll = $(window).scrollTop(),
                scrollLimit = $('body').hasClass('catalogsearch-result-index') ? 300 : 129;
                    // correct offset height if on category  page 129
                    // correct offset height if on category search result page 300
                    // category description block normal mobile height 117px
                    
                scrollLimit = $('.catSeeMore').hasClass('expanded') ? (scrollLimit - 117) + $('.category-description').height() : scrollLimit;
            
            // Only do the logic on mobile
            if ($(window).width() < 768) {
                if (!sticky.hasClass('active')) {
                    if(scroll >= scrollLimit){
                        sticky.addClass('fixed');
        
                        sticky.css('width', 'auto');
                        $('#layered-filter-block').css("top", parseFloat($('.mobile-top-block').height()) + "px");
        
                        if(!$('.sidewide-message').length && sticky.hasClass('fixed')) {
                            $('#layered-filter-block').css("top", parseFloat($('.header-m-primary-container').height()) + 'px');
                        }
                    } else {
                        sticky.removeClass('fixed');
                        $('#layered-filter-block').css("top", "");
                        sticky.css('width', '100vw');
                    }
                } else {
                    if(scroll >= scrollLimit){
                        sticky.addClass('fixed');
                    }
                    $('.header-container3').hide();
                }
            }
          });
    });

});
