Scripts were pulled from Google VRView git repo: https://github.com/googlevr/vrview/tree/master/

Date of script download:
4/20/2018

Latest commit on their master branch at the time of download:
bd12336b97eccd4adc9e877971c1a7da56df7d69

The repo is unstable (changes directly to master, no tagging)

## Used to generate the VR iFrame wrapper
`vrview.js`

## Used to populate the VR iFrame view-port
`three.js`
`embed.js`

* Note that embed.js contains hardcoded styling for the VR View canvas elements (EG: buttons)
  * These can be removed + rewritten in `~/app/httpdocs/inc/skins/{skin}/tpl/pages/minimal/page.less` as necessary
  * Some of them already have been
  * Search for "REWMOD" to see changes made in embed.js
    * Removed an inline style from fullscreen icon
    * Removed some inline styling from VR icon
    * Removed a conditional that wasn't returning true properly
      * Was blocking necessary deprecated polyfill from loading in IE/Edge/FF
    * Made crossOrigin image request pass user credentials to resolve issue with non-SSL sites
    * Added variable-exists checks to resolve console errors on certain browsers
    * update loading image filepath
    * Stopped some code from loading in IE11 that was preventing the VR from rendering
    
