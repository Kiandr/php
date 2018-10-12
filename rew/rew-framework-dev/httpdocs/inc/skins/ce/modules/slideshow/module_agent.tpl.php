<div id="<?=$this->getUID(); ?>">
    <div class="slides">
        <?php for ($i=0; $i<$slides_count; $i++) {
            $class = '';
            if ($i == 0) {
                $class = 'active';
            } else if($i ==  1) {
                $class = 'next';
            } else if ($i == $slides_count - 1) {
                $class = 'prev';
            }
            ?>
            <div class="slide <?=$class; ?>"><?=$slides[$i]; ?></div>
        <?php } ?>
    </div>
    <div class="slides-controls">
        <button class="button button--ghost -left -pad-sm">
            <svg class="prev" viewBox="0 0 9.9 16.8" width="18" height="18" role="img" aria-labelledby="title">
                <title>Previous</title>
                <desc>View the Previous slide</desc>
                <path d="M309.14,324.53a1.5,1.5,0,0,1-1.06-2.56l5.84-5.84-5.84-5.84a1.5,1.5,0,0,1,2.12-2.12l6.9,6.9a1.5,1.5,0,0,1,0,2.12l-6.9,6.9A1.5,1.5,0,0,1,309.14,324.53Z" transform="translate(-307.64 -307.72)"></path>
            </svg>
        </button>
        <button class="button button--ghost -right -pad-sm">
            <svg class="next" viewBox="0 0 9.9 16.8" width="18" height="18" role="img" aria-labelledby="title">
                <title>Next</title>
                <desc>View the next slide</desc>
                <path d="M309.14,324.53a1.5,1.5,0,0,1-1.06-2.56l5.84-5.84-5.84-5.84a1.5,1.5,0,0,1,2.12-2.12l6.9,6.9a1.5,1.5,0,0,1,0,2.12l-6.9,6.9A1.5,1.5,0,0,1,309.14,324.53Z" transform="translate(-307.64 -307.72)"></path>
            </svg>
        </button>
    </div>
</div>
