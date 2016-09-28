<?php
/**
 * Shortcodes
 */

/* PLEASE ADD every new shortcode to the get_shortcodes_help function below (all except columns shortcodes which are includes in inc/tinymce.js.php */

/**
 * Get shortcodes list
 *
 */
function mc_twsi_get_shortcodes_list()
{
    $aHelp = array();

//adding custom items which are not shortcodes but are required for popup.php (eg. nav-menus.php icons)
    if (isset($_GET['custom_popup_items']) && $_GET['custom_popup_items'] == 1 && function_exists('mc_twsi_get_custom_popup_items')) {
        $custom_items = mc_twsi_get_custom_popup_items();
        if (is_array($custom_items)) {
            $aHelp = array_merge($aHelp, $custom_items);
        }
    }

    return $aHelp;
}

