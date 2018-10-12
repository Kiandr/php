(function () {
    //'use strict';

    // Feature background
    var $feature = $('#feature');

    // YouTube Video
    var videoId = $feature.data('video-id');
    if (videoId && videoId.length > 0) {
        var videoMute = $feature.data('video-mute');
        var videoPlay = true;
        var videoPause = false;
        var videoPauseScroll = false;

        switch($feature.data('video-autoplay')) {
            case 'off':
                videoPlay = false;
                break;
            case 'on':
                videoPlay = true;
                break;
            case 'desktop':
                videoPlay = !isMobileDevice();
                break;
            case 'mobile':
                videoPlay = isMobileDevice();
                break;
            default:
                videoPlay = true;
        }

        switch($feature.data('video-autopause')) {
            case 'off':
                videoPause = false;
                break;
            case 'scroll':
                videoPauseScroll = true;
                videoPause = false;
                break;
            case 'view':
                videoPause = true;
                break;
            default:
                videoPause = false;
        }
        $feature.YTPlayer({
            playerVars: {rel: 0, playsinline: 1, controls: 0, autoplay: videoPlay ? 1 : 0},
            fitToBackground: true,
            pauseOnScroll: !!videoPauseScroll,
            pauseOutOfView: !!videoPause,
            mute: !!videoMute,
            videoId: videoId
        });
    }

    // Panoramic Photo
    var panoImg = $feature.data('pano-src');
    if (panoImg && panoImg.length > 0) {
        var pano = $feature.pano({
            img: panoImg,
            interval: 60,
            speed: 10,
            touches: 2
        });
        pano.moveLeft();
    }

    // 360 (VR) Photo
    $('[data-vr-src]').each(function(i,v) {
        $mfeature = $(v);
        var VRImg = $mfeature.data('vr-src');
        if (VRImg && VRImg.length > 0) {
            pannellum.viewer($mfeature.attr('id'), {
                type: 'equirectangular',
                panorama: VRImg,
                autoLoad: true,
                autoRotate: -2,
                mouseZoom: false,
                showControls: false,
                orientationOnByDefault: true,
                keyboardZoom: false,
                usedKeyNumbers: [16, 17, 27, 37, 38, 39, 40, 107, 109, 173, 187, 189],
                fingerTouches: 2
            });
        }
    });

})();

function isMobileDevice() {
    return (typeof window.orientation !== 'undefined') || (navigator.userAgent.indexOf('IEMobile') !== -1);
}