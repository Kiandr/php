<?php

/**
 * Saved Searches Email Template - Body css partial
 * @var array $style
 */

?>
<style>
@media only screen and (max-width: 660px) {

    img,
    img[class="img"],
    img[class="agent-img"],
    img[class="listing-img"] {
        width: 100% !important;
        height: auto !important;
    }
    /* 011 Mobile Fixes to specific email clients -------------------------------------------------- */
    /* Webkit */
    body,
    table,
    td,
    p,
    a,
    li,
    blockquote {
        -webkit-text-size-adjust: none !important;
    }

    /* iOS Mail */
    body {
        width: 100% !important;
        min-width: 100% !important;
    }

    /* 012 Mobile Page background and main container ----------------------------------------------- */
    .container-outer-td {
        padding: 10px !important;
    }

    .container-inner-table {
        width: 100% !important;
        max-width: 600px !important;
    }

    /* 013 Mobile Header section ------------------------------------------------------------------- */
    .pre-header-table {
        display: none !important;
    }

    .header-logo-img {
        max-width: 318px !important;
        height: auto !important;
    }

    .header-logo-td {
        font-size: 20px !important;
        line-height: 125% !important;
    }

    /* 014 Mobile Body section --------------------------------------------------------------------- */
    .body-td {
        font-size: 18px !important;
        line-height: 125% !important;
    }

    /* 015 Mobile Content -------------------------------------------------------------------------- */
    .column-td {
        display: block !important;
        width: 100% !important;
    }

    h1 {
        font-size: 24px !important;
        line-height: 120% !important;
    }

    h2 {
        font-size: 20px !important;
        line-height: 100% !important;
    }

    h3 {
        font-size: 18px !important;
        line-height: 100% !important;
    }

    h4 {
        font-size: 16px !important;
        line-height: 100% !important;
    }

    /* 016 Mobile Columns -------------------------------------------------------------------------- */
    .listing-img {
        width: 100% !important;
        max-width: 600px !important;
        height: auto !important;
    }

    .column-left,
    .column-right {
        font-size: 16px !important;
        line-height: 125% !important;
    }

    .column-left {
        padding-right: 20px !important;
    }

    .column-right {
        padding-left: 20px !important;
    }

    *[class="table-column"] {
        width: 100% !important;
        height: auto !important;
    }

    *[class="table-column-td"] {
        width: 100% !important;
        height: auto !important;
        padding-top: 0px !important;
        padding-right: 20px !important;
        padding-bottom: 20px !important;
        padding-left: 20px !important;
    }

    *[class="listing-address-td"] {
        width: 100% !important;
        height: auto !important;
        padding-top: 0px !important;
        padding-right: 20px !important;
        padding-bottom: 0px !important;
        padding-left: 20px !important;
    }

    *[class="100p"] {
        width:100% !important;
        height:auto !important;
    }

    /* 017 Mobile listing details ------------------------------------------------------------------ */
    .hero-left {
        padding-bottom: 0px !important;
    }

    .listing-img {
        height: auto !important;
    }

    /* 018 Mobile Agent ---------------------------------------------------------------------------- */
    .agent-img {
        width: 100% !important;
        max-width: 600px !important;
        height: auto !important;
    }

    .agent-td {
        padding-left: 10px;
    }

    .agent-column-td {
        display: block !important;
        width: 100% !important;
    }

    *[class="table-column-td-agent"] {
        width: 100% !important;
        height: auto !important;
        padding-top: 0px !important;
        padding-right: 0px !important;
        padding-bottom: 0px !important;
        padding-left: 0px !important;
        text-align: center;
    }

    /* 019 Mobile Footer section ------------------------------------------------------------------- */
    .footer-td {
        font-size: 14px !important;
        line-height: 115% !important;
    }

    .footer-td a {
        display: block !important;
    }
    .hide {display:none !important;}
} /* end media query (max-width: 660px) */
</style>