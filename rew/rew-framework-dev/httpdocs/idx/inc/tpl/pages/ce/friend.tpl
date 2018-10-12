<?php

// Listing Not Found
if (empty($listing)) {
    echo '<h1>Listing Not Found</h1>';
    echo '<p class="notice notice--negative">The selected listing could not be found. This is probably because the listing has been sold, removed from the market, or simply moved.</p>';

} else {

    // Share Title
    $title  = $listing['Address'] . ', ' . $listing['AddressCity'] . ' Property Listing: '. Lang::write('MLS_NUMBER') . $listing['ListingMLS'];

?>
<div id="social-network-panel" class="share-listing">
    <article class="mar-md">
        <a class="share-link" title="Add to Facebook" href="javascript:var w = window.open('http://www.facebook.com/sharer.php?u=<?=urlencode($listing['url_details']); ?>&amp;t=<?=urlencode($title); ?>', 'sharer', 'toolbar=0,status=0,scrollbars=1,width=660,height=400'); w.focus();">
            <svg width="80px" height="80px" viewBox="0 0 150 150">
                <title>Facebook</title>
                <desc>Share this property on Facebook</desc>
                <g>
                <path id="Blue_1_" fill="#3C5A99" d="M140.637,148.781c4.498,0,8.145-3.646,8.145-8.145V9.363c0-4.498-3.646-8.145-8.145-8.145
                H9.364c-4.499,0-8.145,3.646-8.145,8.145v131.273c0,4.498,3.646,8.145,8.145,8.145H140.637z"/>
                <path id="f" fill="#FFFFFF" d="M103.033,148.781V91.639h19.182l2.873-22.271h-22.055V55.148c0-6.447,1.791-10.842,11.039-10.842
                l11.791-0.002V24.383c-2.039-0.27-9.039-0.875-17.184-0.875c-17.004,0-28.643,10.377-28.643,29.438v16.422H60.806v22.271h19.231
                v57.143H103.033z"/>
                </g>
            </svg> 
            <div>Share this Property on Facebook</div>
        </a>
    </article>
    <article class="mar-md">
        <a class="share-link" title="Share on Twitter" href="javascript:var w = window.open('http://twitter.com/home?status=<?=urlencode('Check out this real estate listing: ' . $listing['url_details']); ?>', 'twittersharer', 'toolbar=0,status=0,scrollbars=1,width=400,height=325'); w.focus();">
            <svg width="80px" height="80px" viewBox="0 0 150 150">
                <title>Twitter</title>
                <desc>Tweet this listing on Twitter</desc>
                <path fill="#429CD6" d="M47.681,134.811c55.56,0,85.956-46.01,85.956-85.925c0-1.323,0-2.618-0.088-3.91
                c5.925-4.286,11.016-9.578,15.07-15.645c-5.521,2.443-11.36,4.053-17.371,4.771c6.326-3.795,11.043-9.718,13.287-16.733
                c-5.924,3.534-12.423,6.009-19.152,7.361c-11.445-12.195-30.599-12.769-42.762-1.325c-7.851,7.364-11.188,18.35-8.714,28.875
                c-24.328-1.238-46.931-12.712-62.274-31.576c-8.021,13.805-3.924,31.46,9.347,40.316c-4.803-0.144-9.505-1.438-13.702-3.794
                c0,0.144,0,0.286,0,0.401c0,14.379,10.138,26.771,24.228,29.62c-4.441,1.207-9.117,1.381-13.646,0.516
                c3.969,12.309,15.3,20.735,28.225,20.993c-10.696,8.397-23.911,12.941-37.513,12.941c-2.399,0-4.803-0.174-7.19-0.462
                C15.201,130.122,31.274,134.811,47.681,134.811"/>
            </svg>
            <div>Tweet this Listing on Twitter</div>
        </a>
    </article>
    <article class="mar-md">
        <a class="share-link" title="Share on Google+" href="javascript:var w = window.open('https://plusone.google.com/share?url=<?=urlencode($listing['url_details']); ?>', 'gplusshare', 'toolbar=0,status=0,scrollbars=1,width=600,height=450'); w.focus();">
            <svg width="80px" height="80px" viewBox="-71 -71 150 150">
                <title>Google Plus</title>
                <desc>Share this listing on Google plus</desc>
                <path fill="#DB4437" d="M-23.317-5.369v18.736c0,0,17.953,0,25.772,0C-1.446,25.854-7.697,32.106-23.317,32.106
                c-15.603,0-27.304-12.485-27.304-28.106c0-15.602,11.701-28.104,27.304-28.104c7.819,0,13.269,3.115,17.953,7.035
                c3.918-3.92,3.918-4.703,13.286-14.053c-7.817-7.037-18.736-11.721-31.239-11.721C-49.071-42.843-70.15-21.772-70.15,4
                s21.08,46.843,46.833,46.843c39.022,0,48.41-33.572,45.275-56.212C13.371-5.369-23.317-5.369-23.317-5.369z M61.781-4.585v-16.404
                H50.045v16.404H32.892V7.117h17.153v17.169h11.736V7.117H78.15V-4.585H61.781z"/>
            </svg>
            <div>Share this page on Google+</div>
        </a>
    </article>
    <article class="mar-md">
        <a class="share-link" title="Send to Friend" target="_top" href="mailto:?subject=<?=rawurlencode('Listing from ' . $_SERVER['HTTP_HOST'] . ' - ' . $listing['Address']);?>&body=<?=rawurlencode('While searching for property on ' . Settings::getInstance()->SETTINGS['URL_IDX'] . ', I thought you might be interested in the following property ' . html_entity_decode(Lang::write('MLS_NUMBER')) . $listing['ListingMLS'] . ".\r\n\r\nThe URL below will take you to the property's details:\r\n" . $listing['url_details']);?>">
            <svg width="80px" height="80px" viewBox="-71 -71 150 150">
                <title>Email</title>
                <desc>Send a link to this property via email</desc>
                <path fill="#D0C900" d="M-70.438-51.828v18.609L4,4l74.438-37.219v-18.609H-70.438z M-70.438-14.609v74.438H78.438v-74.438L4,22.609
                L-70.438-14.609z"/>
            </svg> 
            <div>Send a link to this property via Email.</div>
        </a>
    </article>
</div>
<?php

}