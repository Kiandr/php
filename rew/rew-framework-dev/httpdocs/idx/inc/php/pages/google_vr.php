<?php

// Get Requested Listing
$listing = requested_listing();

// Request Object
$request = new Http_Request;

// Use Minimal Template (No header/footer/etc..)
$page->setTemplate('minimal');

if (!empty($listing)) {

    $page_title = Lang::write('IDX_DETAILS_PAGE_TITLE', $listing);
    $meta_keyw  = Lang::write('IDX_DETAILS_META_KEYWORDS', $listing);
    $meta_desc  = Lang::write('IDX_DETAILS_META_DESCRIPTION', $listing);

    if (!empty($listing['enhanced']['google-vr-360-image'])) {

        // This pageload will create and display the VR iFrame
        if (empty($request->get('image'))) {

            // VR Wrapper JS lib
            $page->addJavascript('inc/js/lib/google_vr/vrview.js', 'google-vr');
            $page->addJavascript(sprintf(
                "const renderVR = function() {
                    new VRView.Player('#elm-google-vr-window', {
                        image: '%s',
                        is_stereo: %s,
                        is_autopan_off: true,
                        width: '100%%',
                        height: '100%%'
                    });
                };
                const reRenderVR = function() {
                    // Have to re-render VR for landscape view on iOS devices due to height calculation issues
                    var is_iOS = /(iPad|iPhone|iPod)/g.test( navigator.userAgent );
                    if (is_iOS) {
                        switch (window.orientation) {
                            case 90 || -90:
                                var vrWrapper = document.getElementById('elm-google-vr-window'),
                                    iFrameEl = vrWrapper.querySelector('iframe');
                                vrWrapper.removeChild(iFrameEl);
                                setTimeout(function(){
                                    renderVR();
                                }, 200);
                                break;
                        }
                    } else {
                        const body = document.body;
                        body.style.height = '100%%';
                        body.style.width = '100%%';
                    }
                };
                window.addEventListener('load', renderVR);
                window.addEventListener('orientationchange', reRenderVR);",
                $listing['enhanced']['google-vr-360-image'],
                ($listing['enhanced']['google-vr-image-type'] === 'vr-360' ? 'true' : 'false')
            ), 'page');

            // Google VR - iFrame Wrapper
            echo sprintf('<div id="elm-google-vr-window">
                    <a href="%s">
                        <svg class="webvr-button exit" draggable="false" title="Back to Listing Details" viewBox="0 0 11.1 11.1">
                            <polygon points="11.1 1.41 9.68 0 5.55 4.13 1.41 0 0 1.41 4.13 5.55 0 9.68 1.41 11.1 5.55 6.96 9.68 11.1 11.1 9.68 6.96 5.55 11.1 1.41"/>
                        </svg>
                    </a>
                </div>',
                $listing['url_details']
            );

        // This pageload will populate the iFrame content
        } else {
            include(sprintf(
                '%s%s/tpl/misc/google-vr-viewport.tpl.php',
                Settings::getInstance()->DIRS['SKINS'],
                $this->getSkin()->getDirectory()
            ));
            $page->addJavascript(
                "WebVRConfig = {
                    BUFFER_SCALE: 0.5,
                    TOUCH_PANNER_DISABLED: false
                };",
                'page'
            );
            $page->addJavascript('inc/js/lib/google_vr/three.js', 'google-vr');
            $page->addJavascript('inc/js/lib/google_vr/embed.js', 'google-vr');
        }
    // Listing Not Enhanced with Google VR
    } else {
        header(sprintf('Location: %s', $listing['url_details']));
    }
// Listing Not Found
} else {
    header('HTTP/1.1 404 NOT FOUND');
    $page_title = Lang::write('IDX_DETAILS_PAGE_TITLE_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_keyw  = Lang::write('IDX_DETAILS_META_KEYWORDS_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_desc  = Lang::write('IDX_DETAILS_META_DESCRIPTION_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
}
