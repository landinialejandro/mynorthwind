
                </div>
            </section>
            <!-- /.content FLUID-->

        </div>
        <!-- /.content-wrapper -->
            <!-- Main Footer -->
    <?php if(!$_REQUEST['Embedded']){ ?>
            <footer class="main-footer">
                <!-- To the right -->
                <div class="pull-right hidden-xs">
                <?php echo $LTE_globals['footer-right-text']; ?>
                </div>
                <!-- Default to the left -->
                <?php echo $LTE_globals['footer-left-text']; ?>
                <!-- Add footer template above here -->
                <div class="clearfix"></div>
                <?php if(!defined('APPGINI_SETUP') && is_file(dirname(__FILE__) . '/../hooks/footer-extras.php')){ include(dirname(__FILE__).'/../hooks/footer-extras.php'); } ?>
            </footer>
            <?php 
        include "control_sidebar_lte.php";
    } ?>
        </div>
        <!-- /.wrapper -->
        <!-- /boody -->
        <script src="<?php echo PREPEND_PATH; ?>resources/lightbox/js/lightbox.min.js"></script>
        <script src="<?php echo PREPEND_PATH; ?>LAT/dist/js/demo.js"></script>
        <script> 
        //resuelve el conflicto entre bootstrap y prototypejs
        //http://www.softec.lu/site/DevelopersCorner/BootstrapPrototypeConflict
        //demo page
        //http://jsfiddle.net/dgervalle/hhBc6/

        jQuery.noConflict();
        if (Prototype.BrowserFeatures.ElementExtensions) {
            var disablePrototypeJS = function (method, pluginsToDisable) {
                    var handler = function (event) {
                        event.target[method] = undefined;
                        setTimeout(function () {
                            delete event.target[method];
                        }, 0);
                    };
                    pluginsToDisable.each(function (plugin) { 
                        jQuery(window).on(method + '.bs.' + plugin, handler);
                    });
                },
                pluginsToDisable = ['collapse', 'dropdown', 'modal', 'tooltip', 'popover', 'tab'];
            disablePrototypeJS('show', pluginsToDisable);
            disablePrototypeJS('hide', pluginsToDisable);
        }
        </script>
    </body>
</html>