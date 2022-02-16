jQuery(document).ready(function(){
    jQuery('.faqholder, .faq').on('click', function(){
        jQuery(this).siblings().removeClass('expanded');
        jQuery(this).toggleClass('expanded');
    });

    const sidetopics = jQuery('.sidetopics');
    const current = jQuery('.sidetopics .current');
    if(current.index() != 0){
        sidetopics.find('ul').prepend(current);
    }
    sidetopics.css('opacity', 1);
    if(sidetopics && window.outerWidth < 767){
        sidetopics.on('click', function(e){
            if(!sidetopics.hasClass('expanded')){
                e.preventDefault();
                sidetopics.toggleClass('expanded')
            }
        })
    }
    jQuery('body').on('click', function(e){
        if(jQuery(e.target).closest('.sidetopics').length == 0){
            sidetopics.removeClass('expanded');
        }
    });
});