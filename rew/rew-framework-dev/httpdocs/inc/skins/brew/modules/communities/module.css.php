<?php

// Module Path
$module_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__FILE__));

?>
/* <style> */

#<?=$this->getUID() ; ?> {
	line-height: 1.44;
	overflow: hidden;
	margin-bottom: 25px;
}

#<?=$this->getUID() ; ?> .description {
	min-height: 175px;
	line-height:20px !important;
}
#<?=$this->getUID() ; ?> h1{
	margin-bottom:0.5em;
}

#<?=$this->getUID() ; ?> h2{
	line-height:normal;
}
#<?=$this->getUID() ; ?> h2.heading {
	font-size: 17px;
	margin-bottom: 4px;
	margin-top:20px;
	padding-bottom:3px;
	border:none;
}

#<?=$this->getUID() ; ?> table {
	border-top: solid 1px #333;
	width: 100%
}

#<?=$this->getUID() ; ?> tr.even {
	background:#F4F4F4
}

#<?=$this->getUID() ; ?> td {
	color: #000;
	padding: 5px
}

#<?=$this->getUID() ; ?> td.stats {
	padding-left: 0;
	color: #333;
}

#<?=$this->getUID() ; ?> .community-image {
	margin: 5px 0 0 0; padding: 0;
	border: 5px solid #ccc;
	overflow: hidden;
}

#<?=$this->getUID() ; ?> .community-image img {
	display: block;
}

#<?=$this->getUID() ; ?> small {
	display: block;
	text-align: center;
	padding: 20px;
}

#<?=$this->getUID() ; ?> .community-thumbnails {
	margin: 15px 0 10px 0;
	overflow: hidden;
}

#<?=$this->getUID() ; ?> .community-thumbnails a {
	float: left;
    display: block;
    margin-bottom: 5px;
    width: 32%;
    margin-right: 2%;
}
#<?=$this->getUID() ; ?> .community-thumbnails a:nth-child(3n){
    margin-right:0;
}
#<?=$this->getUID() ; ?> .community-thumbnails img {
	border: 4px solid #ccc;
	width: 100%;
	float:left;
	-webkit-box-sizing: border-box;	/* Safari/Chrome, other WebKit */
	-moz-box-sizing: border-box;	/* Firefox, other Gecko */
	box-sizing: border-box;			/* Opera/IE 8+ */
}

#<?=$this->getUID() ; ?> .community-thumbnails a:last-child img {
	margin-right: 0;
}

#<?=$this->getUID() ; ?> .community-links {
	clear: both;
	margin: 0;
	background: #666;
	overflow: hidden;
}

#<?=$this->getUID() ; ?> .community-links span {
	width: 50%;
	display: block;
	float:left;
}

#<?=$this->getUID() ; ?> .community-links a {
	display: block;
	border-left: 1px solid #fff;
	padding: 10px 0 10px 45px;
	background: url(<?=$module_path; ?>/img/arrow.png) no-repeat 10px center;
	color: #fff !important;
}

#<?=$this->getUID() ; ?> .x6.o0 {
	width: 48%;
	float: left;
	left: 0; margin: 0;
}

#<?=$this->getUID() ; ?> .x6.o6 {
	width: 48%;
	float: right;
	left: 0; margin: 0;
}

@media only screen and (max-width: 480px) {
	#<?=$this->getUID() ; ?> .x6.o0 {
		width: 100%;
		float: none;
	}
	#<?=$this->getUID() ; ?> .x6.o6 {
		width: 100%;
		float: none;
	}
}