<?php 
	$brew_bath = str_replace($_SERVER['DOCUMENT_ROOT'],'', realpath(dirname(__FILE__))).'/img/'.$this->config('style').'/';
	$skin_path = str_ireplace('brew', Settings::getInstance()->SKIN, $brew_bath);
	$path = file_exists($_SERVER['DOCUMENT_ROOT'] . $skin_path)? $skin_path : $brew_bath;
?>

.webicon { background-size: 100% 100% !important; margin: 0 0 0 2px; display: inline-block; width: 30px; height: 30px; text-indent: -999em; text-align: left; -moz-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.5); -webkit-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.5); box-shadow: 0 1px 0 rgba(0, 0, 0, 0.5); -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px; }
.webicon:hover {-moz-box-shadow: 0 2px 0 rgba(0, 0, 0, 0.25); -webkit-box-shadow: 0 2px 0 rgba(0, 0, 0, 0.25); box-shadow: 0 2px 0 rgba(0, 0, 0, 0.25);}
.webicon.small { width: 20px; height: 20px; -moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px; }
.webicon.large { width: 30px; height: 30px; -moz-border-radius: 6px; -webkit-border-radius: 6px; border-radius: 6px; }

/* Facebook */
.no-svg .webicon.facebook { background: url("<?=$path;?>webicon-facebook-m.png"); }
.no-svg .webicon.facebook.large { background: url("<?=$path;?>webicon-facebook.png"); }
.no-svg .webicon.facebook.small { background: url("<?=$path;?>webicon-facebook-s.png"); }
.svg .webicon.facebook { background: url("<?=$path;?>webicon-facebook.svg"); }

/* Twitter */
.no-svg .webicon.twitter { background: url("<?=$path;?>webicon-twitter-m.png"); }
.no-svg .webicon.twitter.large { background: url("<?=$path;?>webicon-twitter.png"); }
.no-svg .webicon.twitter.small { background: url("<?=$path;?>webicon-twitter-s.png"); }
.svg .webicon.twitter { background: url("<?=$path;?>webicon-twitter.svg"); }

/* Google+ */
.no-svg .webicon.googleplus { background: url("<?=$path;?>webicon-googleplus-m.png"); }
.no-svg .webicon.googleplus.large { background: url("<?=$path;?>webicon-googleplus.png"); }
.no-svg .webicon.googleplus.small { background: url("<?=$path;?>webicon-googleplus-s.png"); }
.svg .webicon.googleplus { background: url("<?=$path;?>webicon-googleplus.svg"); }

/* LinkedIn */
.no-svg .webicon.linkedin { background: url("<?=$path;?>webicon-linkedin-m.png"); }
.no-svg .webicon.linkedin.large { background: url("<?=$path;?>webicon-linkedin.png"); }
.no-svg .webicon.linkedin.small { background: url("<?=$path;?>webicon-linkedin-s.png"); }
.svg .webicon.linkedin { background: url("<?=$path;?>webicon-linkedin.svg"); }

/* Pinterest */
.no-svg .webicon.pinterest { background: url("<?=$path;?>webicon-pinterest-m.png"); }
.no-svg .webicon.pinterest.large { background: url("<?=$path;?>webicon-pinterest.png"); }
.no-svg .webicon.pinterest.small { background: url("<?=$path;?>webicon-pinterest-s.png"); }
.svg .webicon.pinterest { background: url("<?=$path;?>webicon-pinterest.svg"); }

/* RSS */
.no-svg .webicon.rss { background: url("<?=$path;?>webicon-rss-m.png"); }
.no-svg .webicon.rss.large { background: url("<?=$path;?>webicon-rss.png"); }
.no-svg .webicon.rss.small { background: url("<?=$path;?>webicon-rss-s.png"); }
.svg .webicon.rss { background: url("<?=$path;?>webicon-rss.svg"); }

/* Youtube */
.no-svg .webicon.youtube { background: url("<?=$path;?>webicon-youtube-m.png"); }
.no-svg .webicon.youtube.large { background: url("<?=$path;?>webicon-youtube.png"); }
.no-svg .webicon.youtube.small { background: url("<?=$path;?>webicon-youtube-s.png"); }
.svg .webicon.youtube { background: url("<?=$path;?>webicon-youtube.svg"); }

/* Instagram */
.no-svg .webicon.instagram { background: url("<?=$path;?>webicon-instagram-m.png"); }
.no-svg .webicon.instagram.large { background: url("<?=$path;?>webicon-instagram.png"); }
.no-svg .webicon.instagram.small { background: url("<?=$path;?>webicon-instagram-s.png"); }
.svg .webicon.instagram { background: url("<?=$path;?>webicon-instagram.svg"); }

/* Yelp */
.no-svg .webicon.yelp { background: url("<?=$path;?>webicon-yelp-m.png"); }
.no-svg .webicon.yelp.large { background: url("<?=$path;?>webicon-yelp.png"); }
.no-svg .webicon.yelp.small { background: url("<?=$path;?>webicon-yelp-s.png"); }
.svg .webicon.yelp { background: url("<?=$path;?>webicon-yelp.svg"); }