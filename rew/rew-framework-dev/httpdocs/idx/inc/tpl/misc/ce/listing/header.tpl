<?php

// Listing title
if (!empty($listing['ListingTitle'])) {
    echo sprintf(
        '<h1 class="-mar-vertical-md">%s</h1>',
        $listing['ListingTitle']
    );

//  Listing header
} else if (empty($_COMPLIANCE['details']['remove_heading'])) {
    echo sprintf(
        '<h1 class="-mar-vertical-md">$%s - %s, %s, %s</h1>',
        Format::number($listing['ListingPrice']),
        $listing['Address'],
        $listing['AddressCity'],
        $listing['AddressState']
    );
}

// Back link
echo sprintf(
    '<a class="idx-details-back-link button button--sm -text-xs -mar-bottom-md" href="%s">%s</a>',
    $listing['url_details'],
    'Back to Property Details'
);