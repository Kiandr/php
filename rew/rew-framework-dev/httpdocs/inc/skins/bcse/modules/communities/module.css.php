<?php $uid = $this->getUID(); ?>
#<?=$uid; ?> .community-links a {

}

#<?=$uid; ?> .community-thumbnails {
	margin: 15px 0 10px 0;
	overflow: hidden;
}

#<?=$uid; ?> .community-thumbnails a {
	float: left;
    display: block;
    margin-bottom: 5px;
    width: 32%;
    margin-right: 2%;
}
#<?=$uid; ?> .community-thumbnails a:nth-child(3n){
    margin-right:0;
}
#<?=$uid; ?> .community-thumbnails img {
	border: 4px solid #fff;
	width: 100%;
	float:left;
	-webkit-box-sizing: border-box;	/* Safari/Chrome, other WebKit */
	-moz-box-sizing: border-box;	/* Firefox, other Gecko */
	box-sizing: border-box;			/* Opera/IE 8+ */
	box-shadow: 0 0 4px rgba(0,0,0,.1);
	-mox-box-shadow: 0 0 4px rgba(0,0,0,.1);
	-webkit-box-shadow: 0 0 4px rgba(0,0,0,.1);
}

#<?=$uid; ?> .community-thumbnails a:last-child img {
	margin-right: 0;
}

#<?=$uid; ?> .community-image img{
	box-shadow: 0 0 4px rgba(0,0,0,.1);
	-mox-box-shadow: 0 0 4px rgba(0,0,0,.1);
	-webkit-box-shadow: 0 0 4px rgba(0,0,0,.1);
	border: 4px solid #fff;
}