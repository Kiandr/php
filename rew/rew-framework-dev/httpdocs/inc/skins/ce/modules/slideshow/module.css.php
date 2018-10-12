#<?=$this->getUID(); ?> {
    height: 100%;
    position: absolute;
    width: 100%;
    z-index: 1000;
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
    opacity: 0;
    transition: opacity 1s ease;
}

#<?=$this->getUID(); ?> .slide.active {
    opacity: 1;
}
