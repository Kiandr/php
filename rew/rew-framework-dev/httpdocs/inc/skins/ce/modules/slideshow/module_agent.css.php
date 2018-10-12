#<?=$this->getUID(); ?> .slides {
    height: 100%;
    width: 100%;
    z-index: 1000;
}

#<?=$this->getUID(); ?> .slides .slide {
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

#<?=$this->getUID(); ?> .slides .slide.active {
    opacity: 1;
}

#<?=$this->getUID(); ?> .slides-controls button.-left {
    margin-left: -40px;
}

#<?=$this->getUID(); ?> .slides-controls button.-right {
    margin-right: -40px;
}

#<?=$this->getUID(); ?> .slides-controls button.-left .prev {
    transform: rotate(180deg);
}
