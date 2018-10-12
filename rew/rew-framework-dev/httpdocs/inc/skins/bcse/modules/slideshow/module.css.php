/* <style> */

#<?=$this->getUID(); ?> {
	height: 100%;
	position: absolute;
	width: 100%;
	z-index: 0;
	top: 0;
}

#<?=$this->getUID(); ?> .slide {
	width: 100%;
	height: 100%;
	position: absolute;
	background-position: center center;
	background-repeat: no-repeat;
	background-size: cover;
	z-index: 1;
	display: none;
}


#<?=$this->getUID(); ?> .slide.active {
	display: block;
}

@media only screen and (max-width: 480px) {
	#<?=$this->getUID(); ?> .slide {
		background-image: none !important;
	}
}