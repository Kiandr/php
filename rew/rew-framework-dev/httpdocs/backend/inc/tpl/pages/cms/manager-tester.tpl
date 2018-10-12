<div class="block">

    <h1><?= __('Demo'); ?></h1>

    <p><?= __('Standard Single-Image Selection (EG Profile Photo, Logo)'); ?></p>

    <div class="field">
        <label class="field__label"><?= __('Feature Image'); ?></label>
        <div class="photo-manager">
            <input type="file" class="photo-manager__file">
            <a href="#" class="photo-manager__photo photo-manager__photo--add-new" onclick="$(this).parent().find('.photo-manager__file').trigger('click')"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-add"/></svg></a>
        </div>
    </div>

</div>


<div class="block">

    <p><?= __('Standard Single-Image Selection (EG Profile Photo, Logo) with Selected image state'); ?></p>

    <div class="field">
        <label class="field__label"><?= __('Feature Image'); ?></label>
        <div class="photo-manager">
            <input type="file" class="photo-manager__file">
            <a href="#" class="photo-manager__photo" onclick="$('.popup-container, .photo-manager-popup').removeClass('is-hidden'); return false;"><img src="/backend/img/logo.png" alt=""/></a>
        </div>
    </div>

</div>

<div class="block">

    <p><?= __('Standard Multiple-Image Selection (EG Gallery Photos/Slideshow)'); ?></p>

    <div class="field">
        <label class="field__label"><?= __('Slideshow Images'); ?></label>
        <div class="photo-manager">
            <input type="file" class="photo-manager__file">
            <a href="#" class="photo-manager__photo" onclick="$('.popup-container, .photo-manager-popup').removeClass('is-hidden'); return false;"><img src="https://medianovak.com/wp-content/uploads/2014/12/free-photography-business-tips-1.jpg" alt=""/></a>
            <a href="#" class="photo-manager__photo"><img src="http://static.wixstatic.com/media/42e0c7_3b0a88987906404c93b14c3471b264ca.jpg/v1/fill/w_584,h_889,al_c,q_90,usm_0.66_1.00_0.01/42e0c7_3b0a88987906404c93b14c3471b264ca.jpg" alt=""/></a>
            <a href="#" class="photo-manager__photo"><img src="/backend/img/logo.png" alt=""/></a>
            <a href="#" class="photo-manager__photo photo-manager__photo--add-new" onclick="$(this).parent().find('.photo-manager__file').trigger('click')"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-add"/></svg></a>
        </div>
    </div>


    <p><?= __('Features to be added later, as time permits:'); ?></p>

    <ul>
        <li><?= __('Ability to set Alt Text'); ?></li>
        <li><?= __('Swap Image button on image popup (saves a step for the user)'); ?></li>
        <li><?= __('Drag &amp; Drop Upload'); ?></li>
        <li><?= __('Drag sort on multiple image selection'); ?></li>
        <li><?= __('Gallery Management, Allow selection from gallery'); ?></li>
    </ul>


</div>


<div class="popup-container is-hidden" onclick="$('.popup-container, .photo-manager-popup').addClass('is-hidden'); return false;">

    <div class="popup photo-manager-popup is-hidden">
        <div class="bar photo-manager-popup__head">
            <span class="bar__title">Photo1.jpg</span>
            <span class="bar__actions">
                <a href="#" class="bar__action" onclick="$('.popup-container, .photo-manager-popup').addClass('is-hidden'); return false;"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-close"/></svg></a>
            </span>
        </div>
        <div class="photo-manager-popup__body">
            <img src="https://medianovak.com/wp-content/uploads/2014/12/free-photography-business-tips-1.jpg" alt=""/>
        </div>
        <div class="bar photo-manager-popup__foot">
            <input type="text" placeholder="<?= __('Alt Text'); ?>" />
            <span class="bar__actions">
                <a href="#" class="bar__action"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-trash"/></svg></a>
            </span>
        </div>
    </div>

</div>



<style>

    .photo-manager                  { overflow: hidden; }
    .photo-manager__photo           { float: left; margin: 0 24px 24px 0; width: 180px; max-width: 100%; position: relative; border-radius: 2px; }
    .photo-manager__photo--add-new  { background: #e1e1e1; }
    .photo-manager__photo .icon,
    .photo-manager__photo img       { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 100%; max-height: 100%; object-fit: contain; }
    .photo-manager__photo .icon     { height: 72px !important; width: 72px !important; }

    .photo-manager__photo img       {
        display: block;
        box-shadow: 0 1px 1px #ccc;
    }

   .photo-manager__photo::after    {
        content: "";
        display: block;
        padding-bottom: 100%;
    }

    .photo-manager__file {
        display: none;
    }

    .photo-manager-popup {
        transform: scale(1);
        background: #40404e;
        color: #fff;
        width: 960px;
        height: 720px;
        max-width: 100%;
        max-height: 100%; 
        border-radius: 2px;
        z-index: 9999;
        display: flex;
        flex-direction: column;

        box-shadow: 0 4px 24px rgba(0,0,0,.33);
    }

    .photo-manager-popup__body {
        flex-grow: 99; position: relative;
    }

    .photo-manager-popup__body img  {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;

        background-color: #fff;
        background-image: linear-gradient(45deg, #e1e1e1 25%, transparent 25%), linear-gradient(-45deg, #e1e1e1 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #e1e1e1 75%), linear-gradient(-45deg, transparent 75%, #e1e1e1 75%);
        background-size: 20px 20px;
        background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
    }

    .photo-manager-popup__foot {
        flex-grow: 0; display: flex;
    }

    .photo-manager-popup__foot input {
        flex-grow: 99;
        background: transparent;
        color: #fff;
        border: none;
        text-rendering: optimizeLegibility;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        /* future-feature */
        display: none;
    }

    .popup-container {
        position: fixed; z-index: 9999;
        top: 0; left: 0; right: 0; bottom: 0;
        display: flex; align-items: center;
        justify-content: center;
        opacity 1;
        transition: opacity .2s ease-out;
        background: rgba(64,64,78,.25);
    }

    .is-hidden {
        opacity: 0;
        pointer-events: none;
        transition: opacity .2s ease-out;
    }

    @media only screen and (max-width: 800px) {
        
        
        
    }

</style>