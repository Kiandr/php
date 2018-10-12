var init = function () {
    global.GOOGLE_API_KEY = REW.settings.idx.googleApiKey;

    require('./actions');
    require('./mapping');
    require('./getlocal');
    require('./panel_display');
    require('./quicksearch');
    require('./results');
    require('./panels');
    require('./search');
    require('./saved_searches');
    require('./autocomplete');
};

if (REW && REW.settings) {
    init();
} else {
    // Sometimes this loads insanely fast and the async script is loaded before the footer. In this
    // case, we need to use docready to make sure we don't jump the gun on initialization.
    $(function () {
        init();
    });
}
