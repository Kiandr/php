<?php

/**
 * Saved Searches Email Template - Head css partial
 * @var array $style
 */


/* TOC --------------------------------------------------------------------------------------------- */
/*
001 Fixes to specific email clients
002 Resets
003 Page background and main container
004 Header section
005 Body section
006 Content
007 Columns
008 listing details
009 Agent
010 Footer section
---- MEDIA QUERY STYLES ARE INSIDE BODY ELEMENT ----
011 Mobile Fixes to specific email clients
012 Mobile Page background and main container
013 Mobile Header section
014 Mobile Body section
015 Mobile Content
016 Mobile Columns
017 Mobile listing details
018 Mobile Agent
019 Mobile Footer section
*/

/* 001 Fixes to specific email clients ------------------------------------------------------------- */
?>
<style type="text/css">
/* Outlook */
#outlook a {
    padding: 0;
}

/* Hotmail */
.ReadMsgBody {
    width: 100%;
}

.ExternalClass {
    width: 100%;
}

.ExternalClass,
.ExternalClass p,
.ExternalClass span,
.ExternalClass font,
.ExternalClass td,
.ExternalClass div {
    line-height: 100%;
}

/* WebKit / Windows Mobile */
body,
table,
td,
p,
a,
li,
blockquote {
    -webkit-text-size-adjust: 100%;
    -ms-text-size-adjust: 100%;
}

@-ms-viewport {
    width: device-width;
}

/* Outlook 2007+ */
table,
td {
    mso-table-lspace: 0pt;
    mso-table-rspace: 0pt;
}

/* Internet Explorer */
img {
    -ms-interpolation-mode: bicubic;
}

/* Android 4.4 */
body {
    margin: 0 !important;
}

div[style*="margin: 16px 0"] {
    margin:0 !important;
}

/* 002 Resets -------------------------------------------------------------------------------------- */
body {
    height: 100% !important;
    margin: 0;
    padding: 0;
}

img {
    height: auto;
    border: 0;
    outline: none;
    line-height: 100%;
    text-decoration: none;
}

table {
    border-collapse: collapse !important;
}

body,
.container-outer-table,
.container-outer-td {
    height: 100% !important;
    margin: 0;
    padding: 0;
}

/* 003 Page background and main container styles --------------------------------------------------- */
.container-outer-td {
    padding: 20px;
}

.container-inner-table {
    width: 600px;
    border: 1px solid <?= $style["template_border"]; ?>;
}

body,
.container-outer-table {
    background-color: <?= $style["page_bg"]; ?>;
}

/* 004 Header section ------------------------------------------------------------------------------ */
.pre-header-table {
    border-bottom: 1px solid <?= $style["template_border"]; ?>;
}

.pre-header-content {
    padding-top: 10px;
    padding-right: 20px;
    padding-bottom: 10px;
    padding-left: 20px;
    background-color: <?= $style["preheader_bg"]; ?>;
    font-family: <?= $style["font_stack"]; ?>;
    font-size: 10px;
    line-height: 125%;
    text-align: left;
    color: <?= $style["preheader_text"]; ?>;
}

.pre-header-content-intro {
    padding-left: 20px;
}

.pre-header-content-browser {
    padding-left: 0px;
    text-align: center;
}

a.pre-header-link:link,
a.pre-header-link:visited,
    /* Yahoo! Mail */ a.pre-header-link .yshortcuts {
    font-weight: normal;
    text-decoration: underline;
    color: <?= $style["preheader_link"]; ?>;
}

.header-table {
    border-bottom: 1px solid <?= $style["template_border"]; ?>;
    background-color: <?= $style["logo_bg"]; ?>;
}

.header-logo-td {
    padding-top: 0;
    padding-right: 0;
    padding-bottom: 0;
    padding-left: 0;
    vertical-align: middle;
    font-family: <?= $style["font_stack"]; ?>;
    font-size: 20px;
    font-weight: bold;
    line-height: 100%;
    text-align: center;
    color: <?= $style["body_text"]; ?>;
}

.header-logo-td a:link,
.header-logo-td a:visited,
    /* Yahoo! Mail Override */ .header-logo-td a .yshortcuts {
    font-weight: normal;
    text-decoration: underline;
    color: <?= $style["body_link"]; ?>;
}

.header-logo-img {
    max-width: 590px;
    width: auto;
    height: auto;
    padding-top: 16px;
    padding-bottom: 16px;
}

/* 005 Body section -------------------------------------------------------------------------------- */
.body-table {
    background-color: <?= $style["body_bg"]; ?>;
}

/* 006 Content ------------------------------------------------------------------------------------- */
h1 {
    display: block;
    margin-top: 0;
    margin-right: 0;
    margin-bottom: 20px;
    margin-left: 0;
    font-family: <?= $style["font_stack"]; ?>;
    font-size: 24px;
    font-style: normal;
    font-weight: normal;
    line-height: 140%;
    letter-spacing: normal;
    text-align: left;
    color: <?= $style["body_text"]; ?> !important;
}

h2 {
    display: block;
    margin-top: 0;
    margin-right: 0;
    margin-bottom: 10px;
    margin-left: 0;
    font-family: <?= $style["font_stack"]; ?>;
    font-size: 22px;
    font-style: normal;
    font-weight: normal;
    line-height: 115%;
    letter-spacing: normal;
    text-align: left;
    color: <?= $style["body_text"]; ?> !important;
}

.body-td {
    padding-top: 20px;
    padding-right: 20px;
    padding-bottom: 20px;
    padding-left: 20px;
    font-family: <?= $style["font_stack"]; ?>;
    font-size: 16px;
    line-height: 150%;
    text-align: left;
    color: <?= $style["body_text"]; ?>;
}

.body-td a:link,
.body-td a:visited,
    /* Yahoo! Mail Override */ .body-td a .yshortcuts {
    font-weight: normal;
    text-decoration: underline;
    color: <?= $style["body_link"]; ?>;
}

.body-td img {
    display: inline;
    max-width: 600px;
    height: auto;
}

.introduction-table .body-td {
    padding-bottom: 0px;
}

/* 007 Columns ------------------------------------------------------------------------------------- */
.column-td {
    padding-top: 20px;
    width: 270px;
}

.columns-table {
    border-top: none;
    border-bottom: 1px solid <?= $style["template_border"]; ?>;
    background-color: <?= $style["body_bg"]; ?>;
}

.column-left {
    padding-top: 0;
    padding-right: 10px;
    padding-bottom: 20px;
    padding-left: 20px;
    font-family: <?= $style["font_stack"]; ?>;
    font-size: 14px;
    line-height: 150%;
    text-align: left;
    color: <?= $style["body_text"]; ?>;
}

.column-right {
    padding-top: 0;
    padding-right: 20px;
    padding-bottom: 20px;
    padding-left: 10px;
    font-family: <?= $style["font_stack"]; ?>;
    font-size: 14px;
    line-height: 150%;
    text-align: left;
    color: <?= $style["body_text"]; ?>;
}

/* 008 listing details ----------------------------------------------------------------------------- */
.hero-table {
    background-color: <?= $style["body_bg"]; ?>;
    border-bottom: none;
}

.hero-td {
    padding-top: 0px;
    padding-right: 20px;
    padding-bottom: 0px;
    padding-left: 20px;
    width: 100%;
}

.listing-img {
    display: inline;
    max-width: 270px;
    height: auto;
}

.hero-details-td {
    padding-top: 0px;
    padding-right: 20px;
    padding-bottom: 20px;
    padding-left: 20px;
    width: 100%;
    font-family: <?= $style["font_stack"]; ?>;
    text-align: left;
    color: <?= $style["body_text"]; ?>;
}

.listing-price {
    margin-top: 4px;
    margin-right: 0px;
    margin-bottom: 8px;
    margin-left: 0px;
    font-size: 20px;
    color: <?= $style["body_text"]; ?>;
}

.listing-details {
    margin-top: 0px;
    margin-right: 0px;
    margin-bottom: 12px;
    margin-left: 0px;
    font-size: 16px;
    color: <?= $style["body_text"]; ?>;
}

.listing-details-divider {
    display: inline-block;
    padding-right: 4px;
    padding-left: 4px;
    color: #cbcbcb;
}

a.btn-more-details:link,
a.btn-more-details:visited,
    /* Yahoo! Mail Override */ a.btn-more-details .yshortcuts {
    display: block;
    margin-top: 8px;
    margin-right: 0px;
    margin-bottom: 8px;
    margin-left: 0px;
    border-radius: <?= $style["button_radius"]; ?>;
    border-top-right-radius: <?= $style["button_radius"]; ?>;
    border-bottom-right-radius: <?= $style["button_radius"]; ?>;
    border-bottom-left-radius: <?= $style["button_radius"]; ?>;
    border-top-left-radius: <?= $style["button_radius"]; ?>;
    background-color: <?= $style["button_bg"]; ?>;
    font-size: 15px;
    line-height: 40px;
    text-align: center;
    text-decoration: none;
    color: <?= $style["button_text"]; ?> !important;
}

.view-all-td {
    font-family: <?= $style["font_stack"]; ?>;
    font-size: 16px;
    line-height: 140%;
    color: <?= $style["body_text"]; ?>;
}

/* 009 Agent --------------------------------------------------------------------------------------- */
.agent-column-td {
    padding-top: 0px;
    width: 290px;
}

.agent-img {
    max-width: 290px;
    height: auto;
}

.agent {
    margin-right: 20px;
    font-family: <?= $style["font_stack"]; ?>;
    font-size: 14px;
    line-height: 140%;
    color: <?= $style["body_text"]; ?>;
}

.agent-name {
    margin-top: 20px;
    margin-bottom: 0px;
    font-size: 22px;
}

.agent-role {
    margin-bottom: 20px;
    font-size: 18px;
    font-style: italic;
}

.agent-info {
    margin-bottom: 20px;
}

.agent-info-lnk:link,
.agent-info-lnk:visited {
    color: <?= $style["body_link"]; ?>;
}

.agent-link:link,
.agent-link:visited {
    color: <?= $style["body_link"]; ?>;
}

.agent-phone {
    margin-bottom: 20px;
}

/* 010 Footer section ------------------------------------------------------------------------------ */

.footer-table {
    background-color: <?= $style["footer_bg"]; ?>;
}

.footer-td {
    padding-top: 20px;
    padding-right: 20px;
    padding-bottom: 12px;
    padding-left: 20px;
    font-family: <?= $style["font_stack"]; ?>;
    font-size: 14px;
    line-height: 150%;
    text-align: left;
    color: <?= $style["footer_text"]; ?>;
}

.footer-td-address,
.footer-td-copyright,
.footer-td-subscription {
    padding-top: 0px;
}

.footer-td-address p {
    font-size: 14px;
    line-height: 20px;
}

.footer-td-subscription {
    font-size: 11px;
}

.footer-td a:link,
.footer-td a:visited,
    /* Yahoo! Mail Override */ .footer-td a .yshortcuts, .footer-td a span {
    font-weight: normal;
    text-decoration: underline;
    color: <?= $style["footer_link"]; ?>;
}

.footer-social-icon {
    display: inline-block !important;
    margin-right: 8px;
    text-decoration: none;
}
.hide {display:none !important;}
</style>