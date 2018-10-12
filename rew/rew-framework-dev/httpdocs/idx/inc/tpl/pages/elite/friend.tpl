<?php

// Listing Not Found
if (empty($listing)) {
    include $page->locateTemplate('idx', 'misc', 'not-found');
    return;
}

?>

<div class="modal-header">
    <h3 id="share-it">Share this Article with a Friend.</h3>
</div>

<?php
    // Share Title
    $title  = $listing['Address'] . ', ' . $listing['AddressCity'] . ' Property Listing: '. Lang::write('MLS_NUMBER') . $listing['ListingMLS'];
?>
<div class="modal-body">
    <div id="social-network-panel" class="uk-grid">
        <article class="uk-width-1-2 uk-width-small-1-4"><a title="Add to Facebook" href="javascript:var w = window.open('http://www.facebook.com/sharer.php?u=<?=urlencode($listing['url_details']); ?>&amp;t=<?=urlencode($title); ?>', 'sharer', 'toolbar=0,status=0,scrollbars=1,width=660,height=400'); w.focus();"><img src="/img/facebook_001.jpg" alt=""> <h4>Facebook</h4> Share this Property on Facebook</a></article>
        <article class="uk-width-1-2 uk-width-small-1-4"><a title="Share on Twitter" href="javascript:var w = window.open('http://twitter.com/home?status=<?=urlencode('Check out this real estate listing: ' . $listing['url_details']); ?>', 'twittersharer', 'toolbar=0,status=0,scrollbars=1,width=400,height=325'); w.focus();"><img src="/img/Twitter_001.jpg" alt=""> <h4>Twitter</h4> Tweet this Listing on Twitter</a></article>
        <article class="uk-width-1-2 uk-width-small-1-4"><a title="Share on Google+" href="javascript:var w = window.open('https://plusone.google.com/share?url=<?=urlencode($listing['url_details']); ?>', 'gplusshare', 'toolbar=0,status=0,scrollbars=1,width=600,height=450'); w.focus();"><img src="/img/Google_Plus_001.jpg" alt=""> <h4>Google+</h4> Share this page on Google+</a></article>
        <article class="uk-width-1-2 uk-width-small-1-4"><a title="Send to Friend" href="mailto:?subject=<?=rawurlencode('Listing from ' . $_SERVER['HTTP_HOST'] . ' - ' . $listing['Address']);?>&body=<?=rawurlencode('While searching for property on ' . Settings::getInstance()->SETTINGS['URL_IDX'] . ', I thought you might be interested in the following property ' . html_entity_decode(Lang::write('MLS_NUMBER')) . $listing['ListingMLS'] . ".\r\n\r\nThe URL below will take you to the property's details:\r\n" . $listing['url_details']);?>"><img src="/img/Email_001.jpg" alt=""> <h4>Via. Email</h4> Send a link to this property via Email.</a></article>
    </div>
</div>
