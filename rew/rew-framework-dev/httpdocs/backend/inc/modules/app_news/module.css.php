.news_sprites {
	background-image : url(/backend/img/icons/alert.png);
	background-color : transparent;
	background-repeat : no-repeat;
	height : 16px;
	width : 16px;
	display : inline-block;
	vertical-align : middle;
 }

.news_icon_warn { background-position  : -0px -0px; }

.news_icon_info { background-position  : -16px -0px; }

.news_icon_alert { background-position  : -32px -0px; }
 
.news_icon_ad { background-image: none; }
 
#app_sidebar h1 {
	color: #fff;
	background-color: #0066CC;
	border-radius: 5px 5px 0 0;
	margin: 10px 15px 0px 15px;
	padding: 5px 10px;
	width: 150px;
	text-align: center;
	font-weight: bold;
	font-size: 20px;
	text-shadow: 0 1px 1px rgba(0,0,0,0.25);
}
 
#rew-news {
	width: 170px;
	padding: 0px;
	margin: 0px 15px;
	background-color: white;
	border-radius: 0 0 5px 5px;
}

#rew-news li {
	border-bottom: 1px solid rgba(0,0,0,.25);
	position:relative;
	margin: 0;
}

#rew-news ul {
	margin:0;
	padding:0;
	border-bottom: none;
}

#rew-news h2 {
	text-align: center;
	font-weight: bold;
}
 
#rew-news h2.warn {
	background-color: #FFFFE6;
}

#rew-news h2.info {
	background-color: #E6F4FF;
}

#rew-news h2.alert {
	background-color: #FFE6E6;
}

#rew-news .news_sprites {
	position:absolute;
	top:2px;
	left:2px;	
}

#rew-news p {
	font-size: 10px;
	padding: 7px;
}

#rew-news ul li a {
	display: block;
	text-align: center;
	color: #000;
	text-shadow: none;
	padding: 0;
	background-color: #F0F0F0;
}

#rew-news ul:last-child a {
	border-radius: 0 0 5px 5px;
}