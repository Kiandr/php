<?php

/**
 * Saved Searches Email Template - Head css partial
 * @var array $style
 */
?>

<style type="text/css">

    /* Client */

    #outlook a {
        padding: 0; /* Force outlook to provide a "view in browser" message */
    }

    .ReadMsgBody {
        width: 100%; /* Force hotmail to display emails at full width */
    }

    .ExternalClass {
        width: 100%; /* Force hotmail to display emails at full width */
    }

    .ExternalClass,
    .ExternalClass p,
    .ExternalClass span,
    .ExternalClass font,
    .ExternalClass td,
    .ExternalClass div {
        line-height: 100%; /* Force hotmail to display normal line spacing */
    }

    body,
    table,
    td,
    a {
        /* Prevent webkit and windows mobile changing default text sizes */
        -webkit-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
    }

    table,
    td {
        /* Remove spacing between tables in outlook 2007 and up */
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
    }

    img {
        /* Allow smoother rendering of resized image in IE */
        -ms-interpolation-mode: bicubic;
    }

    /* Reset */

    body {
        height: 100% !important;
        margin: 0;
        padding: 0;
    }

    img {
        border: 0;
        height: auto;
        line-height: 100%;
        outline: none;
        text-decoration: none;
    }

    table {
        border-collapse: collapse !important;
    }

    /* Prevent blue links in iOS */
    .link a {
        color: <?= $style["body_text"]; ?>;
        text-decoration: none;
    }

    /* Mobile */

    @media screen and (max-width: 600px) {

        /* Use attribute selectors to prevent Yahoo from applying mobile styles on desktop */

        /* Content */
        td[class="logo"] img {
            margin: 0 auto !important;
            text-align: center;
        }

        td[class="title-padding"] {
            padding: 30px 10px 0px 10px !important;
        }

        td[class="text-padding"] {
            padding: 10px 10px 10px 10px !important;
        }

        /* Utils */
        table[class="wrap"] {
            width: 100% !important;
        }

        td[class="p2"] {
            padding: 20px 20px 20px 20px !important;
        }

        td[class="pt2"] {
            padding-top: 20px !important;
        }

        td[class="pb2"] {
            padding-bottom: 20px !important;
        }

        td[class="plr1"] {
            padding: 0 10px 0 10px !important;
        }

        td[class="plr2"] {
            padding: 0 20px 0 20px !important;
        }

        td[class="hidden"] {
            display: none !important;
        }

        img[class="img"] {
            width: 100% !important;
            height: auto !important;
        }
    }
</style>

<style>
    /* https://litmus.com/blog/update-banning-blue-links-on-ios-devices */
    a[x-apple-data-detectors] {
        color: inherit !important;
        text-decoration: none !important;
        font-size: inherit !important;
        font-family: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
    }
</style>