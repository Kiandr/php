### Tint Options for Vision Template

These are the tint options available for the hero section:

```css
.cloak /* This class name adds the default and should already be applied */
.cloak--vignette /* Adds the 'frame' look (dark tint on the outsides, light in the middle). */
.cloak--dusk  /* Dark tint on top third, light near middle, dark on bottom half (think when the sun is setting) */
.cloak--clear /* removes the tint */
.cloak--full /* Adds an even dark tint to the whole cover element */
.cloak--top /* Adds dark tint to the top half, gets lighter the closer to the middle it gets */
.cloak--bottom /* Adds dark tint to the bottom half, gets lighter the closer to the middle it gets. */
```

```html
<!-- Cover Page Template -->
<div id="feature" class="hero hero--cover">
    <div class="hero__fg"></div>
    <div id="cover__background" class="hero__bg">
        <div class="cloak cloak--*"></div> <!-- tint option applied to this element -->
        <!-- Some media content would be here (YT video, slideshow ...)-->
    </div>
</div>
```

The tint can then be applied to the element based on which option the user selects in the backend

```php
<div class="cloak<?=(!empty($tintOverlay) ? ' cloak--' . $tintOverlay : ''); ?>"></div>
```

The config options are located in `inc/skins/ce/tpl/cover/config.json`

```json
"tint": {
    "_enabled": true,
    "type": "select",
    "title": "Tint Overlay",
    "options": [
        { "value": "full", "title": "Full" },
        { "value": "top", "title": "Top Half" },
        { "value": "bottom", "title": "Bottom Half" },
        { "value": "vignette", "title": "Framed" },
        { "value": "dusk", "title": "Dusk" },
        { "value": "clear", "title": "None" }
    ],
    "default": "full"
}
```