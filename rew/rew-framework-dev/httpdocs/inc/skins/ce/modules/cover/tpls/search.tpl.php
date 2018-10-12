<form action="/idx/" method="get">
    <input type="hidden" name="feed" value="">
    <div class="input -pill">
        <?php
        // Display location search
        echo IDX_Panel::get('Location', [
            'inputClass' => 'autocomplete location',
            'placeholder'	=> sprintf(
                'City, %s, Address, %s or %s #',
                Locale::spell('Neighborhood'),
                Locale::spell('Zip'),
                Lang::write('MLS')
            ),
            'toggle' => false,
        ])->getMarkup();
        ?>
        <button type="submit" class="btn -pill">Search</button>
    </div>
</form>