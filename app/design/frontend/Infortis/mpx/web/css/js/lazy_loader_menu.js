/**
 * Created by Wouter on 5/4/17.
 */
jQuery(document).ready(function(){
    const splitCount = 10;

    function splitItems() {
        var text = jQuery('#translated').text();
        jQuery('#mainmenu .mega .level0.nav-submenu').each(function(){
            var childElements = jQuery(this).children('li');
            if(childElements.length > splitCount){
                var subTabsneeded = Math.floor(childElements.length / splitCount);
                var classList = jQuery(this).attr('class');
                for(var j = 0; j <= subTabsneeded; j++) {
                    if(j === 0) {
                        jQuery(this).append(jQuery('<p class="showmore">' + text + '</p>'));
                        continue;
                    }
                    var subtab = jQuery('<ul class="' + classList + ' subtab right"></ul>');
                    for (var i = j*splitCount; i < childElements.length; i++) {
                        subtab.append(jQuery(childElements[i]));
                    }
                    subtab.append('<div class="subback"></div>');
                    if(j != subTabsneeded) subtab.append(jQuery('<p class="showmore">' + text + '</p>'));
                    jQuery(this).parent().append(subtab);
                }
            }
        });
    }
    
    function formatMainMenu() {
        var nonMegaItems = jQuery('#mainmenu .nav-item--only-subcategories');
        var other = nonMegaItems[nonMegaItems.length -1];
        var translated = jQuery('#translatedbrand').text();
        var otherBrands = jQuery('<div class="otherbrands nav-item level-0 parent"><span>'+translated+'</span><div class="opener"></div></div>');
        otherBrands.insertAfter(jQuery('#mainmenu > ul .mega').last());
        var holder = jQuery('<div class="outerbrandholder flexer"><div class="brandimageholder"><img src="/skin/frontend/ultimo/default/images/PlaceHolder_2.png"/></div></div>')
        var panel = jQuery('<div class="brandspanel flexervert"></div>');
        otherBrands.append(holder.prepend(panel));
        var clone = nonMegaItems.clone(true, true);
        addBrands(panel, clone.slice(0,-1));
        nonMegaItems.hide();
        other.show();
    }

    function addBrands(parent, otherBrandsMenuItems){
        otherBrandsMenuItems.each(function(){
            var row = jQuery('<div class="otherbrandrow flexer"></div>');
            parent.append(row.append(collectTopLevelItems(jQuery(this))));
            collectSubmenuItems(row, jQuery(this));
        });
    }
    
    function collectTopLevelItems(menuitem) {
        return menuitem.find('> a');
    }
    
    function collectSubmenuItems(parent, menuitem) {
        var wrapper = jQuery('<div class="otherproducts flexervert"></div>');
        menuitem.children('.nav-submenu').children('.nav-item--parent').each(function(){
            formatProduct(wrapper, jQuery(this));
            parent.append(wrapper);
        });

    }
    
    function formatProduct(parent, brandSubmenu) {
        var outerwrap = jQuery('<div class="productcasewrap flexer"></div>')
        var casewrapper = jQuery('<div class="othercaseswrap flexervert"></div>');
        var cat_text = brandSubmenu.children().first().text();
        var cases =  brandSubmenu.find('.menu_products_li_a');
        var width = 0;
        cases.each(function(){
            jQuery(this).children('span').remove();
            var text = jQuery(this).text();
            jQuery(this).text('');
            casewrapper.append(jQuery(this).append('<span class="ruler">'+cat_text+'</span><span class="caselink">'+text+'</span>'));
        });
        parent.append(outerwrap.append(casewrapper));

    }

    function formatSubMenu() {
        jQuery('#mainmenu > ul li.mega .grid12-3 .level3.nav-submenu.nav-panel.nav-panel--dropdown').each(function(){
            jQuery(this).closest('.nav-panel-inner').find('.grid12-9').append(jQuery(this));
        })
    }

    if(jQuery(window).width() > 768 && jQuery('.mega').length > 0) {

        // JS FOR DESKTOP MENU CHANGES
        formatMainMenu();
        formatSubMenu();
        splitItems();
        var menu_elem;
        var subMenuHover = false;
        var lastHovered;
        var timeOutId;
        jQuery(document).on({
            mouseenter: function () {
                if(timeOutId){
                    clearTimeout(timeOutId);
                    jQuery('.hovered').removeClass('hovered');
                    jQuery(menu_elem).css({
                        'display': 'none',
                        'opacity': 0
                    })
                }
                var index = jQuery(this).parent().parent().hasClass('subtab') ? jQuery(this).parent().parent().index() *splitCount +jQuery(this).parent().index() :jQuery(this).parent().index();
                menu_elem = jQuery(this).parent().closest('.nav-panel-inner').find('.grid12-9 ul')[index];
                jQuery(menu_elem).css({
                    'display': 'flex',
                    'opacity': 1
                });
            },
            mouseleave: function () {
                var menu_item = menu_elem;
                timeOutId = setTimeout(function(){
                    if(subMenuHover){
                        return;
                    }
                    jQuery(menu_item).css({
                        'display': 'none',
                        'opacity': 0
                    })
                }, 3000)
                jQuery(this).parent().addClass('hovered')
                lastHovered = jQuery(this).parent();
            }
        }, '#mainmenu .nav-panel-inner .grid12-3 .nav-item a');

        jQuery(document).on({
            mouseenter: function() {
                subMenuHover = true;
                lastHovered.addClass('hovered');
                jQuery(this).css({
                    'display':'flex',
                    'opacity': 1
                })
            },
            mouseleave: function() {
                subMenuHover = false;
                jQuery(this).css({
                    'display':'none',
                    'opacity': 0
                })
                lastHovered.removeClass('hovered');
            }
        }, '#mainmenu .nav-panel-inner .grid12-9 > ul');

        jQuery(document).on('click', '.showmore', function(){
            jQuery(this).parent().addClass('goleft')
            jQuery(this).parent().next().removeClass('right');
        });

        jQuery(document).on('click', '.subback', function(){
            jQuery(this).parent().addClass('right');
            jQuery(this).parent().prev().removeClass('goleft');
        });

        jQuery(document).on({
            mouseenter: function(){
                jQuery(this).prev().addClass('hovered')
            },
            mouseleave: function(){
                jQuery(this).prev().removeClass('hovered')
            }
        }, '.brandspanel .otherproducts');
    }



    jQuery("#mainmenu > ul > li").on("mouseenter", function(){
        if(jQuery(window).width() < 945){
            return;
        }
        var elemAll = jQuery(this).find('.loadimage');
        for(var i = 0; i<elemAll.length; i++){
            var elem = elemAll[i];
            var src = jQuery(elem).attr('data-src');
            jQuery(elem).replaceWith('<img class="hide-below-960" src="'+src+'"/>');
        }
    });
    jQuery(window).on("resize", function(){
        if(jQuery(window).width() > 945){
            jQuery("#mainmenu > ul > li").on("mouseenter", function(){
                var elemAll = jQuery(this).find('.loadimage');
                for(var i = 0; i<elemAll.length; i++){
                    var elem = elemAll[i];
                    var src = jQuery(elem).attr('data-src');
                    jQuery(elem).replaceWith('<img class="hide-below-960" src="'+src+'"/>');
                }
            });
            jQuery('.otherbrands').show();
            jQuery('#mainmenu .nav-item--only-subcategories').hide().last().show();
        }else if(jQuery(window).width() < 945){
            jQuery('.otherbrands').hide();
            jQuery('#mainmenu .nav-item--only-subcategories').show();
        }
    });
});