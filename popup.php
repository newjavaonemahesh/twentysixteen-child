<?php
/**
 * Shortoces popup window.
 * Used in page builder and page editor (shortcodes drop down list)
 * If we have  $_GET['pb_item_id'] and $_GET['builder'] defined it means it's page builer
 * 
 */

$path = realpath(dirname(__FILE__));
$position = strrpos($path,'wp-content');
$wp_path = substr($path, 0, $position);
require_once($wp_path.'wp-load.php');

include STYLESHEETPATH . '/ShortcodesTinyMcePopup.class.php';

$data_field = '';
if (isset($_GET['pb_item_id']) && intval($_GET['pb_item_id']) && isset($_GET['builder']) && !empty($_GET['builder']))
{
	$field_id = intval($_GET['pb_item_id']);
	$data_field = $_GET['builder'].'_'.$_GET['pb_item_id'];
}

//callback function fired on save
$callback = '';
if (isset($_GET['callback']) && !empty($_GET['callback'])) {
	$callback = $_GET['callback'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body>
	
<?php 
global $wp_version;
if ($wp_version >= 3.9) {
    include STYLESHEETPATH .'/popup-content.php';
} else {
    include STYLESHEETPATH .'/popup-content-old.php';
}
?>
<form >
<?php

$oShortcodesTinyMcePopup = new ShortcodesTinyMcePopup($_GET['shortcode']);
$oShortcodesTinyMcePopup -> display();

if (!empty($data_field))
{
	$button = __('Save','framework');;
}
else
{
	$button = __('Insert Shortcode','framework');
}

?>
<div id="shortcode-form-container"></div>
<input id="insert-shortcode" name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php echo $button; ?>">
</form>
</body>
</html>