<?php

/**
 * Profile_Report_Generic_Bootstrap
 * Extends the generic report to use REW-specific markup & styles
 *
 */
class Profile_Report_UIKit_REW extends Profile_Report_Generic
{

    /**
     * Table row class for errors
     * @var string
    */
    protected $_class_row_error = 'uk-alert uk-alert-danger';

    /**
     * Table row class for warnings
     * @var string
     */
    protected $_class_row_warning = 'uk-alert uk-alert-warning';

    /**
     * Table row class for success
     * @var string
     */
    protected $_class_row_success = 'uk-alert uk-alert-success';

    /**
     * Flag memory snapshot report rows as errors for entries
     * that use this much percent of the application's max allowed memory
     * @var float
     */
    protected $_percent_of_app_memory_error = 40;

    /**
     * Flag memory snapshot report rows as warnings for entries
     * that use this much percent of the application's max allowed memory
     * @var float
     */
    protected $_percent_of_app_memory_warning = 30;

    /**
     * Flag memory snapshot report rows as successful for entries
     * that reclaim this much percent of the application's max allowed memory
     * @var float
     */
    protected $_percent_of_app_memory_success = 30;

    /**
     * Get the report's markup
     * @return string
     */
    public function getHTML()
    {
        ob_start();
        ?>
        <div id="profile-report-controls" draggable="true" title="You can Drag &amp; Drop this" style="position: fixed; top: 0; right: 0; padding: 10px; z-index: 9999;">
            <a class="uk-button uk-positive" id="profile-report-show" data-uk-modal="{target:'#profile-report'}">
                <?=$this->microsecondsToString($this->_total_duration); ?>
            </a>
        </div>
        <div id="profile-report" class="uk-modal">
            <div class="uk-modal-dialog uk-modal-dialog-large">
                <a class="uk-modal-close uk-close"></a>
                <div class="uk-modal-header">
                    <h2>Profiler</h2>
                </div>
                <div>
                    <?=parent::getHTML();?>
                </div>
            </div>
        </div>
        <script>
            <?= $this->getUIJavascript(); ?>
        </script>
        <?= $this->scriptForReport(); ?>

        <?php
        return ob_get_clean();
    }

    /**
     * Gets drag and drop JS
     * window.onload incase app.min.js loads late
     * @return string
     */
    protected function getUIJavascript()
    {
        ob_start();
        ?>
        window.onload = function () {
            var $report = $('#profile-report'),
                posCookie = 'profile-report-pos';

            function getCookie(cname) {
                var name = cname + "=";
                var decodedCookie = decodeURIComponent(document.cookie);
                var ca = decodedCookie.split(';');
                for(var i = 0; i <ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }

            function setCookie(cname, cvalue, exdays) {
                var d = new Date();
                d.setTime(d.getTime() + (exdays*24*60*60*1000));
                var expires = "expires="+ d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            }

            var BREW_Profiler = {
                'drag_start' : function(event) {
                    rect = event.target.getBoundingClientRect();
                    event.dataTransfer.setData("text/plain",
                    (parseInt(rect.left,10) - event.clientX) + ',' + (parseInt(rect.top,10) - event.clientY));
                },
                'drag_over' : function(event) {
                    event.preventDefault();
                    return false;
                },
                'drag_id' : 'profile-report-controls',
                'obj' : null,
                'get_obj' : function() {
                    if (BREW_Profiler.obj === null || typeof BREW_Profiler.obj !== 'object') {
                        BREW_Profiler.obj = document.getElementById(BREW_Profiler.drag_id);
                    }
                    return BREW_Profiler.obj;
                },
                'set_pos': function (top, left) {
                    var pctrl = BREW_Profiler.get_obj();
                    pctrl.style.right = null;
                    pctrl.style.left = left + 'px';
                    pctrl.style.top = top + 'px';
                },
                'drop' : function(event) {
                    var offset = event.dataTransfer.getData("text/plain").split(',');
                    var left = event.clientX + parseInt(offset[0],10);
                    var top = event.clientY + parseInt(offset[1],10);
                    setCookie(posCookie, [top, left].join('-'), 7);
                    BREW_Profiler.set_pos(top, left);
                    event.preventDefault();
                    return false;
                },
                'init' : function(drag_id) {
                    if (drag_id && typeof drag_id === 'string' && 0 === drag_id.length) {
                        BREW_Profiler.drag_id = drag_id;
                    }
                    var pctrl = BREW_Profiler.get_obj();
                    pctrl.addEventListener('dragstart', BREW_Profiler.drag_start, false);
                    document.body.addEventListener('dragover', BREW_Profiler.drag_over, false);
                    document.body.addEventListener('drop', BREW_Profiler.drop, false);

                    // Restore button position
                    var posCookieVal = getCookie(posCookie);
                    if (posCookieVal) {
                        var btnPos = posCookieVal.split('-');
                        var btnPosTop = btnPos[0];
                        var btnPosLeft = btnPos[1];
                        if (btnPosTop && btnPosLeft) {
                            BREW_Profiler.set_pos(btnPosTop, btnPosLeft);
                        }
                    }
                    pctrl.style.display = 'block';

                }
            };
            BREW_Profiler.init();

        };
        <?php
        return ob_get_clean();
    }
}
