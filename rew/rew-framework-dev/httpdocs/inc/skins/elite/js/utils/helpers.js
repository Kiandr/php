// Helper functions for various tasks
REW = REW || {};
REW.Helpers = REW.Helpers || {};

REW.Helpers.isMapSearch = function () {
    return REW.settings.app == 'idx-map';
};

REW.Helpers.isMobile = (function () {
    return (typeof window.orientation !== 'undefined') || (navigator.userAgent.indexOf('IEMobile') !== -1 || navigator.userAgent.indexOf('Mobile') !== -1);
})();

REW.Helpers.vis = (function(){
    var stateKey,
        eventKey,
        keys = {
            hidden: 'visibilitychange',
            webkitHidden: 'webkitvisibilitychange',
            mozHidden: 'mozvisibilitychange',
            msHidden: 'msvisibilitychange'
        };
    for (stateKey in keys) {
        if (stateKey in document) {
            eventKey = keys[stateKey];
            break;
        }
    }
    return function(c) {
        if (c) document.addEventListener(eventKey, c);
        return !document[stateKey];
    };
})();

REW.Helpers.truncate = function(el, options){
    // Plugin Options
    var options = $.extend({
        count : 600,
        ending: '...',
        btnClass: '',
        moreText: 'Read More',
        lessText: 'Read Less'
    }, options);

    // Plugin Vars
    var $el       = $(el),
        fullText  = $el.html(),
        truncText = $el.html().substring(0, options.count).split(' ').slice(0, -1).join(' ') + options.ending;
        // get first X characters, sep. into array of words, remove the last full or partial word, join and add ending

    // No Truncating Needed
    if (fullText.length < options.count) {
        return;
    }

    // Default truncate
    $el.html(truncText + ' <a href="#" class="truncate_more '+options.btnClass+'">'+options.moreText+'</a>');

    // Setup 'more' link
    $el.on('click', 'a.truncate_more', function(){
        $el.html(fullText + ' <a href="#" class="truncate_less '+options.btnClass+'">'+options.lessText+'</a>').show();
        return false;
    });

    // Setup 'less' link
    $el.on('click', 'a.truncate_less', function(){
        $el.html(truncText + ' <a href="#" class="truncate_more '+options.btnClass+'">'+options.moreText+'</a>').show();
        return false;
    });
};

module.exports = REW;
