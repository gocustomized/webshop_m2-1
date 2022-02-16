define(['jquery'],
    function($) {
        let width = (window.innerWidth > 0) ? window.innerWidth : document.documentElement.clientWidth;
        if(width >= 768){
            const splitCount = 10;

            function splitItems() {
                var text = $('#translated').text();
                // let text = "Show More";
                $('#mainmenu .level0.nav-submenu').each(function(){
                    let childElements = $(this).children('li');
                    if(childElements.length > splitCount){
                        let subTabsneeded = Math.floor(childElements.length / splitCount);
                        let classList = $(this).attr('class');
                        for(let j = 0; j <= subTabsneeded; j++) {
                            if(j === 0) {
                                $(this).append($('<p class="showmore">' + text + '</p>'));
                                continue;
                            }
                            let subtab = $('<ul class="' + classList + ' subtab right"></ul>');
                            for (let i = j*splitCount; i < childElements.length; i++) {
                                subtab.append($(childElements[i]));
                            }
                            subtab.append('<div class="subback"></div>');
                            if(j != subTabsneeded) subtab.append($('<p class="showmore">' + text + '</p>'));
                            $(this).parent().append(subtab);
                        }
                    }
                });
            }

            $(document).on('click', '.showmore', function(){
                $(this).parent().removeClass('active-section')
                $(this).parent().addClass('goleft')
                $(this).parent().next().addClass('active-section');
                $(this).parent().next().removeClass('right');
            });

            $(document).on('click', '.subback', function(){
                $(this).parent().removeClass('active-section');
                $(this).parent().addClass('right');
                $(this).parent().prev().addClass('active-section');
                $(this).parent().prev().removeClass('goleft');
            });

            let active_section;

            $('.menu-link').on('mouseenter', function(){
                let node = $(this).attr('node');
                if(!node){
                    return;
                }

                let product_section = $('.'+node);

                if(node.indexOf("-product-") < 0){ //check if menu-link is a product
                    if(product_section.length !== 0){
                        clearActiveSection()

                        product_section.removeClass('hidden-product-section');
                        product_section.addClass('active-product-section');
                        active_section = product_section;

                        // Unset height first
                        var groupMenu = $('.' + node + '.active-product-section').closest('.groupmenu-drop.slidedown');
                        groupMenu.css('height','');
                        // reset height based on height of selected items
                        var activeMenu = $('.' + node + '.active-product-section').get(0);
                        var groupMenuTextContentPadding = $('.' + node + '.active-product-section').closest('.text-content').css('padding') ? parseInt($('.' + node + '.active-product-section').closest('.text-content').css('padding')) : 20;
                        if ((activeMenu.offsetHeight + groupMenuTextContentPadding) > parseInt(groupMenu.css('min-height'))) {
                            groupMenu.css('height',(activeMenu.offsetHeight + groupMenuTextContentPadding) + 'px');
                        }

                        if(width >= 768){ //image lazy loading
                            let elemAll = product_section.find('.loadimage');
                            for(var i = 0; i<elemAll.length; i++){
                                var elem = elemAll[i];
                                var src = $(elem).attr('data-src');
                                $(elem).replaceWith('<img class="desktop-view" src="'+src+'" loading="lazy"/>');
                            }
                        }
                    } else {
                        if(active_section){
                            clearActiveSection()
                        }
                    }
                }

            });

            function clearActiveSection(){
                if(active_section){
                    active_section.removeClass('active-product-section');
                    active_section.addClass('hidden-product-section');
                }
            }

            splitItems();
        }

        $(window).on("resize", function(){
            width = $(window).width();
        });

    }
)
