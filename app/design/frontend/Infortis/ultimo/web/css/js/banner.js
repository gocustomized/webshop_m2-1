function createPictureElement(mediaurl){
    var picture = jQuery('<picture></picture>');
    if(settings.image_url_mob){
        picture.append(createSourceElement(mediaurl + settings.image_url_mob, 'mob'));
    }
    if(settings.image_url_tablet){
        picture.append(createSourceElement(mediaurl + settings.image_url_tablet, 'tablet'));
    }
    if(settings.image_url_desk){
        picture.append(createSourceElement(mediaurl + settings.image_url_desk, 'desk'));
        picture.append(jQuery('<img src="'+mediaurl + settings.image_url_desk+'"/>'))
    }

    return picture;
}

function createSourceElement(url, size){
    var source = jQuery('<source></source>');
    source.attr('srcset', createSrcSet(url));
    switch (size){
        case 'desk':
            source.attr('media', '(min-width: 769px)');
            break
        case 'tablet':
            source.attr('media', '(max-width: 768px)');
            break;
        case 'mob':
            source.attr('media', '(max-width: 450px)');
            break;
        default:
            console.log('vanuit default, ', size);
    }

    return source
}

function createSrcSet(url){
    var srcset = '';
    var splitted = url.split('.');
    var extension = splitted.pop();
    var firstpart = splitted.join('.');
    for(var i = 1; i < 4; i ++){
        var komma = i != 3 ? ',' : '';
        srcset += firstpart.slice(0, firstpart.length-2)+i+'x.'+extension+' '+i+"x"+komma;
    }

    return srcset;
}

function createBanner(skinurl, mediaurl){
    var position = settings.position;
    var banner = jQuery('<div class="banner banner'+position+'"><a href="'+settings.link+'"></a></div>');
    var picture = createPictureElement(mediaurl);
    var outerWrap = jQuery('<div class="bannerouterwrap"></div>');
    banner.find('a').append(picture).append(outerWrap.append(createBackgroundElement(skinurl)).append(createTextElement()));
    jQuery('.the-slideshow-wrapper .item').last().append(banner);
    settings = undefined;
}

function createTextElement(){
    var outerTextWrap = jQuery('<div class="bannertextwrap" style="color:'+settings.fontcolor+'"></div>');
    if(settings.title1){
        outerTextWrap.append(jQuery('<h2 class="bannertitle" style="font-family:'+settings.fontfamilytitle+'">'+settings.title1+'</h2>'));
    }
    if(settings.title2){
        outerTextWrap.append(jQuery('<h2 class="bannertitle" style="font-family:'+settings.fontfamilytitle+'">'+settings.title2+'</h2>'));
    }
    if(settings.subtext1){
        outerTextWrap.append(jQuery('<span class="bannersubtext">'+settings.subtext1+'</span>'));
    }
    if(settings.subtext2){
        outerTextWrap.append(jQuery('<span class="bannersubtext">'+settings.subtext2+'</span>'));
    }
    if(settings.buttontext){
        outerTextWrap.append(jQuery('<a class="bannerbut" style="background-color:'+settings.buttoncolor+'">'+settings.buttontext+'</span>'));
    }

    return outerTextWrap;

}

function createBackgroundElement(url){
    if(!settings.bigboycolor) return;
    return jQuery('<div class="backgroundholder" style="opacity:'+settings.opacity+'"><img class="bigboy" src="'+url+'images/banner_big_boys/Big_Boy_'+settings.bigboycolor+'.svg"/><img class="bigboymirror" src="'+url+'images/banner_big_boys/Big_Boy_mirror_'+settings.bigboycolor+'.svg"/><img class="bigboymobile nonmirror mobile-show" src="'+url+'images/banner_big_boys/Big_Boy_mob_'+settings.bigboycolor+'.svg" /><img class="bigboymobile mirror mobile-show" src="'+url+'images/banner_big_boys/Big_Boy_mob_mirror_'+settings.bigboycolor+'.svg" /></div>');
}
