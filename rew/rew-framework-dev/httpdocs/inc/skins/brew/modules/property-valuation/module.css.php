#<?=$this->getUID(); ?> .body-a {
	padding: 30px;
	background: #fff;
}

#<?=$this->getUID(); ?> .body-a h2 {
	margin: 0 0 5px 0;
}

#<?=$this->getUID(); ?> .body-b {
	padding: 30px;
	border-top: 1px solid #ddd;
	background: #f5f5f5;
	overflow: hidden;
}

#<?=$this->getUID(); ?> .body form {
	margin: 0;
}

#<?=$this->getUID(); ?> .section-a {
}

#<?=$this->getUID(); ?> .section-b {
	border: 1px solid #ddd;
}

#<?=$this->getUID(); ?> .section-b .more {
	background: #fff;
	overflow: hidden;
}

#<?=$this->getUID(); ?> .map {
	position: relative;
	background: #ccc;
	border: 1px solid #ccc;
	height: 200px;
}

#<?=$this->getUID(); ?> .map span.ph {
	position: absolute;
	top: 50%; left: 0; right: 0;
	text-align: center;
	margin-top: -15px;
}

#<?=$this->getUID(); ?> .map img {
	max-width: none;
}

#<?=$this->getUID(); ?> .section-a .row {
	overflow: hidden;
	position: relative;
	z-index: 999;
}

#<?=$this->getUID(); ?> .section-a .col-a {
	width: 33.33%;
	left: 100%;
	position: relative;
	margin-left: -100%;
	float: left;
}

#<?=$this->getUID(); ?> .section-a .col-b {
	width: 33.33%;
	left: 100%;
	position: relative;
	margin-left: -66.666%;
	float: left;
}

#<?=$this->getUID(); ?> .section-a .col-c {
	width: 33.33%;
	left: 100%;
	position: relative;
	margin-left: -33.333%;
	float: left;
}

#<?=$this->getUID(); ?> .estimate-values .col {
	text-align: center;
	padding: 20px 0;
	clear: none;
}

#<?=$this->getUID(); ?> .estimate-values strong {
	display: block;
	font-size: 32px;
	line-height: 30px;
}

#<?=$this->getUID(); ?> .col.strong {
	border: 1px solid #ccc; border-bottom: none;
	color: green;
	background: #fff;
	z-index: 999;
	font-weight: bold;
}


#eval-step-location input {
	width: 100%; height: 44px;
	font: 300 20px/30px "source sans pro";
	padding: 0 10px; margin: 0 0 20px 0;
}

#eval-step-location .btnset {
	padding: 0;
}

#eval-step-location button {
	display: block;
	width: 100%;
	font: 300 18px/30px "source sans pro";
	margin: 0;
}

@media only screen and (min-width: 780px) {

	#<?=$this->getUID(); ?> .section-a {
		width: 49%;
		float: right;
	}

	#<?=$this->getUID(); ?> .section-b {
		width: 49%;
		float: left;
	}

	#<?=$this->getUID(); ?> .estimate-values {
		font-weight: 300;
	}

	#<?=$this->getUID(); ?> .estimate-values .col strong {
	    font-size: 26px;
	    line-height: 30px;
	}

}

@media only screen and (min-width: 480px) {

	#<?=$this->getUID(); ?> .signup p {
		margin: 0 0 5px 0 !important;
	}

	#<?=$this->getUID(); ?> .signup .body {
		overflow: hidden;
		padding: 10px;
	}

	#<?=$this->getUID(); ?> .signup img {
		width: 200px; float: left;
	}

	#eval-step-location {
		position: relative;
		height: 44px;
	}

	#eval-step-location input {
		width: 100%; height: 44px;
		position: absolute;
		font: 300 20px/30px "source sans pro";
		padding: 0 10px;
	}

	#eval-step-location .btnset {
		padding: 0;
		width: 100px;
		position: absolute;
		right: 0;
	}

	#eval-step-location button {
		border-radius: 0 3px 3px 0;
		display: block;
		float: left;
		width: 100%;
		font: 300 18px/30px "source sans pro";
		height: 44px;
		margin: 0;
	}

}

#<?=$this->getUID(); ?> .signup {
	position: relative;
	margin: 20px 0;
	clear: both;
	overflow: hidden;
}

#<?=$this->getUID(); ?> .signup h3 {
	margin: 0 0 5px 0 !important;
}

#<?=$this->getUID(); ?> .signup .btnset {
	white-space: normal;
}

#<?=$this->getUID(); ?> .matches {
	background: #eee;
	padding: 30px 30px 0 30px;
	margin-bottom: 15px;
}

#<?=$this->getUID(); ?> .matches header h2 {
	float: left;
	width: auto;
}

#<?=$this->getUID(); ?> .matches header a {
	float: right;
	font-size: 16px;
	padding: 5px;
}

#<?=$this->getUID(); ?> .matches header {
	overflow: hidden;
}

#<?=$this->getUID(); ?> .cta-link {
	cursor: pointer;
	width: 100%;
}

#<?=$this->getUID(); ?> .map-controls {
	border: 1px solid rgba(0, 0, 0, 0.15);
	box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
    border-radius: 2px;
    position: absolute; bottom: -44px;
    font-size: 0;
    background: #fff;
    text-align: center;
    width: 125px;
    height: 28px;
    margin: 5px;
}

#<?=$this->getUID(); ?>-polygon,
#<?=$this->getUID(); ?>-radius {
	-moz-user-select: none;
    color: #000;
    font-size: 11px;
    padding: 1px 6px;
    text-align: center;
    line-height: 24px;
    padding-left: 22px;
    position: relative;
	border: 1px solid rgba(0, 0, 0, 0.15);
	box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
    border-radius: 2px;
	background: #fff;
	min-width: 125px;
	height: 28px;
}

#<?=$this->getUID(); ?>-polygon ul,
#<?=$this->getUID(); ?>-radius ul {
	margin: 0; padding: 0;
	list-style: none;
}

#<?=$this->getUID(); ?>-polygon a,
#<?=$this->getUID(); ?>-radius a {
	font-size: 11px;
	text-decoration: none;
	font-weight: bold;
	outline: 0;
}

#<?=$this->getUID(); ?>-polygon a.edit,
#<?=$this->getUID(); ?>-radius a.edit {
	margin-right: 26px;
}

#<?=$this->getUID(); ?>-polygon a.delete,
#<?=$this->getUID(); ?>-radius a.delete {
   	background: #fff;
   	padding: 0 8px;
   	position: relative;
   	border-left: 1px solid #eee;
   	position: absolute;
   	right: 0; top: 0; bottom: 0;
}

#<?=$this->getUID(); ?>-polygon input,
#<?=$this->getUID(); ?>-radius input {
	position: absolute;
	top: 5px; left: 4px;
}

#<?=$this->getUID(); ?>-polygon .tip,
#<?=$this->getUID(); ?>-radius .tip {
	display: none;
}

#<?=$this->getUID(); ?>-polygon .ico {
	background: url(<?=str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__); ?>/img/map-draw-sprite.png) no-repeat 0 0;
	position: absolute;
	top: 7px; left: 5px;
    display: inline-block;
    height: 13px;
    margin: 0 5px 0 0;
    width: 13px;
}

#<?=$this->getUID(); ?>-radius .ico {
	background: url(<?=str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__); ?>/img/map-draw-sprite.png) no-repeat 0 -20px;
	position: absolute;
	top: 7px; left: 5px;
    display: inline-block;
    height: 13px;
    margin: 0 5px 0 0;
    width: 13px;
}

#<?=$this->getUID(); ?>-polygon li,
#<?=$this->getUID(); ?>-radius li {
	position: static;
}

#<?=$this->getUID(); ?>-polygon a,
#<?=$this->getUID(); ?>-radius a {
	font-weight: normal;
}

#<?=$this->getUID(); ?>-polygon>a,
#<?=$this->getUID(); ?>-radius>a {
	margin-left: -13px;
}

.cma-popup-container {
	padding: 20px;
}

.cma-popup-container form {
	background: #ececec;
	padding: 20px 20px 0 20px;
	border-radius: 5px;
}

.cma-popup-container input,
.cma-popup-container select,
.cma-popup-container textarea {
	border: 1px solid #ccc;
}