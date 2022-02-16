define([
    'jquery'
], function($) {
    return function (config) {
        var slideShowBanner = {
            createPictureElement: function(mediaurl, slideSettings) {
                var settings = slideSettings,
                    picture = $('<picture/>');

                if (settings.image_url_mob) {
                    picture.append(slideShowBanner.createSourceElement(mediaurl + settings.image_url_mob, 'mob'));
                }

                if (settings.image_url_tablet) {
                    picture.append(slideShowBanner.createSourceElement(mediaurl + settings.image_url_tablet, 'tablet'));
                }

                if (settings.image_url_desk) {
                    picture.append(slideShowBanner.createSourceElement(mediaurl + settings.image_url_desk, 'desk'));
                    picture.append($('<img src="'+mediaurl + settings.image_url_desk+'" loading="lazy"/>'))
                }

                return picture;
            },

            createSourceElement: function(url, size) {
                var source = $('<source/>');

                source.attr('srcset', slideShowBanner.createSrcSet(url));

                switch (size) {
                    case 'desk':
                        source.attr('media', '(min-width: 769px)');
                        break;
                    case 'tablet':
                        source.attr('media', '(max-width: 768px)');
                        break;
                    case 'mob':
                        source.attr('media', '(max-width: 450px)');
                        break;
                    default:
                        console.log('vanuit default, ', size);
                }

                return source;
            },

            createSrcSet: function(url) {
                var srcset = '',
                    splitted = url.split('.'),
                    extension = splitted.pop(),
                    firstpart = splitted.join('.');

                for (var i = 1; i < 4; i++) {
                    var komma = i !== 3 ? ',' : '';

                    srcset += firstpart.slice(0, firstpart.length-2)+i+'x.'+extension+' '+i+"x"+komma;
                }

                return srcset;
            },

            parseToJson: function(obj) {
                return Function('"use strict";return (' + obj + ')')();
            },

            getSlideObject: function() {
                var slide = $('.slideshow-wrapper-outer .owl-item');

                if (!slide.length) {
                    return false;
                }

                for (var i = 0; i < slide.length; i++) {
                    var slideScriptObj = $(slide[i]).find('script').text();

                    if (!slideScriptObj.length) {
                        return false;
                    }

                    var objectWithoutComments = slideScriptObj.split('\n').filter(function(line) {
                        return line.indexOf("//") === -1;
                    }).join('\n');

                    var cleanObject = objectWithoutComments.replaceAll("var settings =", ""),
                        preparedObject = slideShowBanner.parseToJson(cleanObject);

                    slideShowBanner.createBanner(config.url, preparedObject, i);
                }
            },

            createBanner: function(mediaurl, slideSettings, slide) {
                var settings = slideSettings || undefined;

                if (!settings) {
                    return false;
                }

                var position = settings.position,
                    banner = $('<div class="banner banner' + position + '"><a href="' + BASE_URL + settings.link + '"></a></div>'),
                    picture = slideShowBanner.createPictureElement(mediaurl, slideSettings),
                    outerWrap = $('<div class="bannerouterwrap"></div>');

                banner.find('a').append(picture).append(outerWrap.append(slideShowBanner.createBackgroundElement(mediaurl, settings)).append(slideShowBanner.createTextElement(slideSettings)));
                $($('.slideshow-wrapper-outer .owl-item')[slide]).append(banner);
                settings = undefined;
            },

            createTextElement: function(slideSettings) {
                var settings = slideSettings;

                var outerTextWrap = $('<div class="bannertextwrap" style="color:'+settings.fontcolor+'"></div>');

                if (settings.title1) {
                    outerTextWrap.append($('<h2 class="bannertitle" style="font-family:'+settings.fontfamilytitle+'">'+settings.title1+'</h2>'));
                }

                if (settings.title2) {
                    outerTextWrap.append($('<h2 class="bannertitle" style="font-family:'+settings.fontfamilytitle+'">'+settings.title2+'</h2>'));
                }

                if (settings.subtext1) {
                    outerTextWrap.append($('<span class="bannersubtext">'+settings.subtext1+'</span>'));
                }

                if (settings.subtext2) {
                    outerTextWrap.append($('<span class="bannersubtext">'+settings.subtext2+'</span>'));
                }

                if (settings.buttontext) {
                    outerTextWrap.append($('<a class="bannerbut" style="background-color:'+settings.buttoncolor+'">'+settings.buttontext+'</span>'));
                }

                return outerTextWrap;
            },

            createBackgroundElement: function(url, settings) {
                if(!settings.bigboycolor) return;
                var bigboyDesk = settings.position == 'left' ? '<img class="bigboy mobile-hide" src="'+url+'images/banner_big_boys/Big_Boy_'+settings.bigboycolor+'.svg" loading="lazy"/>' : '<img class="bigboymirror mobile-hide" src="'+url+'images/banner_big_boys/Big_Boy_mirror_'+settings.bigboycolor+'.svg" loading="lazy"/>';
                var bigboyMob = settings.position == 'left' ? '<img class="bigboymobile nonmirror mobile-show" src="'+url+'images/banner_big_boys/Big_Boy_mob_'+settings.bigboycolor+'.png" loading="lazy"/>' : '<img class="bigboymobile mirror mobile-show" src="'+url+'images/banner_big_boys/Big_Boy_mob_mirror_'+settings.bigboycolor+'.png" loading="lazy"/>';
                return jQuery('<div class="backgroundholder" style="opacity:'+settings.opacity+'">'+bigboyDesk+''+bigboyMob+'</div>');
            }
        };

        return slideShowBanner.getSlideObject();
    };
});
