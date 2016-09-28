<?php

include_once('/shortcodes.php');

function mc_twsi_theme_scripts() {
    $parent_style = 'parent-style';
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );

    wp_register_style ( 'mc_twsi_font-awesome', get_stylesheet_directory_uri() . '/css/font-awesome.css' );
    wp_enqueue_style ( 'mc_twsi_font-awesome' );
}

add_action ( 'wp_enqueue_scripts', 'mc_twsi_theme_scripts' );

function mc_twsi_theme_setup() {
    register_nav_menu('icon-menu',  __ ( 'Icon Menu', 'mctwsi' ));
}

add_action ( 'after_setup_theme', 'mc_twsi_theme_setup' );


if (is_admin()) {
    function mc_twsi_admin_head() {
        ?>
        <script type="text/javascript">
            var popupurl = '<?php echo get_stylesheet_directory_uri() . '/popup.php'; ?>';
            var themeurl = '<?php echo get_stylesheet_directory_uri(); ?>';
        </script>

    <?php
    }

    add_action('admin_head', 'mc_twsi_admin_head');

    function mc_twsi_load_custom_wp_admin_style()
    {

        wp_register_style('custom_wp_admin_css', get_stylesheet_directory_uri() . '/css/admin.css', false);
        wp_register_style('jquery_select2_css', get_stylesheet_directory_uri() . '/css/select2.css', false);
        wp_register_style ( 'mc_twsi_font-awesome', get_stylesheet_directory_uri () . '/css/font-awesome.css' );

        wp_enqueue_style('custom_wp_admin_css');
        wp_enqueue_style('jquery_select2_css');
        wp_enqueue_style('mc_twsi_font-awesome');

        wp_register_script( 'mc_twsi_framework', get_stylesheet_directory_uri().'/js/framework.js',array('jquery'),null);
        wp_register_script( 'jquery_select2', get_stylesheet_directory_uri().'/js/select2.min.js',array('jquery'),null);

        wp_enqueue_script( 'mc_twsi_framework' );
        wp_enqueue_script( 'jquery_select2' );

        add_thickbox();

    }

    add_action('admin_enqueue_scripts', 'mc_twsi_load_custom_wp_admin_style');

}


function mc_twsi_get_custom_popup_items() {

    $custom_popup_items = array(
        array(
            'shortcode' => 'custom_icon',
            'name' => __('Icon','mctwsi'),
            'description' => '',
            'usage' => '',
            'code' => '{icon}',
            'fields' => array(
                'icon' => array(
                    'type' => 'select',
                    'label' => '',
                    'values' => mc_twsi_getFontAwesomeArray(true),
                    'default' => '',
                    'desc' => '',
                    'class' => 'icons-dropdown'
                )
            )
        )
    );

    return $custom_popup_items;
}


function mc_twsi_getFontAwesomeArray($bNoIconChoice = false,$add_icons = null, $return_theme_options = false)
{
    $icons = array();

    if (is_array($add_icons))
    {
        foreach($add_icons as $key => $icon){
            $icons[$key] = $icon;
        }
    }

    if (file_exists(get_stylesheet_directory().'/css/font-awesome.css'))
    {
        $pattern = '/\.(fa-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
        //$pattern = '/\.(fa-(?:\w+(?:-)?)+)/';

        $subject = file_get_contents(get_stylesheet_directory().'/css/font-awesome.css');

        preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);

        foreach($matches as $match){
            $icons[$match[1]] = $match[1];
        }
    }

    if (count($icons) > 0)
    {
        $icons_return = array();
        if ($bNoIconChoice === true)
        {
            $icons_return['no'] = __('no icon','mctwsi');
        }
        $icons_return = array_merge($icons_return,$icons);

        if ($return_theme_options) {
            $icons_theme_options = array();
            foreach ($icons_return as $key => $icon) {
                $icons_theme_options[] = array(
                    'value'       => $key,
                    'label'       => $icon,
                    'src'         => ''
                );
            }
            return $icons_theme_options;
        }

        return $icons_return;
    }

    return array('empty' => __('empty','mctwsi'));

}


new mc_twsi_custom_menu();

class mc_twsi_custom_menu {

    /**
     * Construct
     */
    public function __construct() {

        add_action( 'wp_update_nav_menu_item', array( $this, 'save_custom_menu_items'), 10, 3 );
        add_filter( 'wp_edit_nav_menu_walker', array( $this, 'nav_menu_edit_walker'), 10, 2 );
        add_filter( 'wp_setup_nav_menu_item', array( $this, 'read_custom_menu_items' ) );

    } // end constructor

    /**
     * Read custom menu itesm
     * @param object $menu_item
     * @return type
     */
    function read_custom_menu_items( $menu_item ) {
        $menu_item->megamenu = get_post_meta( $menu_item->ID, '_menu_item_megamenu', true );
        $menu_item->icon = get_post_meta( $menu_item->ID, '_menu_item_icon', true );
        $menu_item->header = get_post_meta( $menu_item->ID, '_menu_item_header', true );
        $menu_item->footer = get_post_meta( $menu_item->ID, '_menu_item_footer', true );
        return $menu_item;
    }

    /**
     * Save custom menu items
     * @param int $menu_id
     * @param int $menu_item_db_id
     * @param array $args
     */
    function save_custom_menu_items( $menu_id, $menu_item_db_id, $args ) {

        if (!isset($_REQUEST['edit-menu-item-megamenu'][$menu_item_db_id])) {
            $_REQUEST['edit-menu-item-megamenu'][$menu_item_db_id] = '';
        }
        $menu_mega_enabled_value = $_REQUEST['edit-menu-item-megamenu'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_megamenu', $menu_mega_enabled_value );

        if (!isset($_REQUEST['edit-menu-item-header'][$menu_item_db_id])) {
            $_REQUEST['edit-menu-item-header'][$menu_item_db_id] = '';
        }
        $header_enabled_value = $_REQUEST['edit-menu-item-header'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_header', $header_enabled_value );

        if (!isset($_REQUEST['edit-menu-item-icon'][$menu_item_db_id])) {
            $_REQUEST['edit-menu-item-icon'][$menu_item_db_id] = '';
        }
        $icon_value = $_REQUEST['edit-menu-item-icon'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_icon', $icon_value );

        if (!isset($_REQUEST['edit-menu-item-footer'][$menu_item_db_id])) {
            $_REQUEST['edit-menu-item-footer'][$menu_item_db_id] = '';
        }
        $footer_value = $_REQUEST['edit-menu-item-footer'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_footer', $footer_value );
    }

    /**
     * Return walker name
     * @return string
     */
    function nav_menu_edit_walker() {
        return 'Walker_Nav_Menu_Edit_Custom';
    }

}

/**
 * This is a copy of Walker_Nav_Menu_Edit class in core
 *
 * Create HTML list of nav menu input items.
 *
 * @package WordPress
 * @since 3.0.0
 * @uses Walker_Nav_Menu
 */
class Walker_Nav_Menu_Edit_Custom extends Walker_Nav_Menu {
    /**
     * @see Walker_Nav_Menu::start_lvl()
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     */
    function start_lvl( &$output, $depth = 0, $args = array() ) {}

    /**
     * @see Walker_Nav_Menu::end_lvl()
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     */
    function end_lvl( &$output, $depth = 0, $args = array() ) {}

    /**
     * @see Walker::start_el()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Menu item data object.
     * @param int $depth Depth of menu item. Used for padding.
     * @param object $args
     */
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $_wp_nav_menu_max_depth;
        $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        ob_start();
        $item_id = esc_attr( $item->ID );
        $removed_args = array(
            'action',
            'customlink-tab',
            'edit-menu-item',
            'menu-item',
            'page-tab',
            '_wpnonce',
        );

        $original_title = '';
        if ( 'taxonomy' == $item->type ) {
            $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
            if ( is_wp_error( $original_title ) )
                $original_title = false;
        } elseif ( 'post_type' == $item->type ) {
            $original_object = get_post( $item->object_id );
            $original_title = $original_object->post_title;
        }

        $classes = array(
            'menu-item menu-item-depth-' . $depth,
            'menu-item-' . esc_attr( $item->object ),
            'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
        );

        $title = $item->title;

        if ( ! empty( $item->_invalid ) ) {
            $classes[] = 'menu-item-invalid';
            /* translators: %s: title of menu item which is invalid */
            $title = sprintf( __( '%s (Invalid)','mctwsi' ), $item->title );
        } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
            $classes[] = 'pending';
            /* translators: %s: title of menu item in draft status */
            $title = sprintf( __('%s (Pending)','mctwsi'), $item->title );
        }

        $title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

        $submenu_text = '';
        if ( 0 == $depth )
            $submenu_text = 'style="display: none;"';

        ?>
    <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
    <dl class="menu-item-bar">
        <dt class="menu-item-handle">
            <span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" <?php echo $submenu_text; ?>><?php _e( 'sub item', 'mctwsi'); ?></span></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
                            echo wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action' => 'move-up-menu-item',
                                        'menu-item' => $item_id,
                                    ),
                                    remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                ),
                                'move-menu_item'
                            );
                            ?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up','mctwsi'); ?>">&#8593;</abbr></a>
							|
							<a href="<?php
                            echo wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action' => 'move-down-menu-item',
                                        'menu-item' => $item_id,
                                    ),
                                    remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                ),
                                'move-menu_item'
                            );
                            ?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down','mctwsi'); ?>">&#8595;</abbr></a>
						</span>
						<a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item','mctwsi'); ?>" href="<?php
                        echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
                        ?>"><?php _e( 'Edit Menu Item','mctwsi' ); ?></a>
					</span>
        </dt>
    </dl>

    <div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
        <?php if( 'custom' == $item->type ) : ?>
            <p class="field-url description description-wide">
                <label for="edit-menu-item-url-<?php echo $item_id; ?>">
                    <?php _e( 'URL','mctwsi'); ?><br />
                    <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
                </label>
            </p>
        <?php endif; ?>
        <p class="description description-thin">
            <label for="edit-menu-item-title-<?php echo $item_id; ?>">
                <?php _e( 'Navigation Label','mctwsi' ); ?><br />
                <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
            </label>
        </p>
        <p class="description description-thin">
            <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
                <?php _e( 'Title Attribute','mctwsi' ); ?><br />
                <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
            </label>
        </p>
        <p class="field-link-target description">
            <label for="edit-menu-item-target-<?php echo $item_id; ?>">
                <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
                <?php _e( 'Open link in a new window/tab','mctwsi' ); ?>
            </label>
        </p>
        <p class="field-css-classes description description-thin">
            <label for="edit-menu-item-classes-<?php echo $item_id; ?>">
                <?php _e( 'CSS Classes (optional)','mctwsi' ); ?><br />
                <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
            </label>
        </p>
        <p class="field-xfn description description-thin">
            <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
                <?php _e( 'Link Relationship (XFN)','mctwsi' ); ?><br />
                <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
            </label>
        </p>
        <p class="field-description description description-wide">
            <label for="edit-menu-item-description-<?php echo $item_id; ?>">
                <?php _e( 'Description','mctwsi' ); ?><br />
                <textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
                <span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.','mctwsi'); ?></span>
            </label>
        </p>

        <p class="field-move hide-if-no-js description description-wide">
            <label>
                <span><?php _e( 'Move','mctwsi' ); ?></span>
                <a href="#" class="menus-move-up"><?php _e( 'Up one','mctwsi' ); ?></a>
                <a href="#" class="menus-move-down"><?php _e( 'Down one','mctwsi' ); ?></a>
                <a href="#" class="menus-move-left"></a>
                <a href="#" class="menus-move-right"></a>
                <a href="#" class="menus-move-top"><?php _e( 'To the top','mctwsi' ); ?></a>
            </label>
        </p>

        <!-- Mega Menu item -->
        <?php
        $value = get_post_meta( $item->ID, '_menu_item_megamenu', true);
        if ($value == "enabled") {
            $value = "checked='checked'";
        }
        ?>
        <div class="clearboth"></div>
        <div class="mega-menu-container">
            <p class="field-link-mega">
                <label for="edit-menu-item-megamenu-<?php echo $item_id; ?>">
                    <input type="checkbox" value="enabled" id="edit-menu-item-megamenu-<?php echo $item_id; ?>" name="edit-menu-item-megamenu[<?php echo $item_id; ?>]" <?php echo $value; ?> />
                    <?php _e( 'Create Mega Menu for this item', 'mctwsi' ); ?>
                </label>
            </p>
        </div>
        <!-- /Mega Menu item -->

        <!-- Header Menu item -->
        <?php
        $value = get_post_meta( $item->ID, '_menu_item_header', true);
        if ($value == "enabled") {
            $value = "checked='checked'";
        }
        ?>
        <div class="clearboth"></div>
        <div class="header-menu-container">
            <p class="field-link-header">
                <label for="edit-menu-item-header-<?php echo $item_id; ?>">
                    <input type="checkbox" value="enabled" id="edit-menu-item-header-<?php echo $item_id; ?>" name="edit-menu-item-header[<?php echo $item_id; ?>]" <?php echo $value; ?> />
                    <?php _e( 'Create header from this item', 'mctwsi' ); ?>
                </label>
            </p>
        </div>
        <!-- /Header Menu item -->

        <!-- Menu Icon item -->
        <?php
        $preview = '';

        $value = get_post_meta( $item->ID, '_menu_item_icon', true);

        if (!empty($value)) {
            $preview = '<i class="'.(strstr($value,'fa-') ? 'fa' : '').' '.$value.'"></i>';
        }

        ?>
        <div class="clearboth"></div>
        <div class="icon-menu-container">
            <p class="field-link-icon">
                <label for="edit-menu-item-icon-<?php echo $item_id; ?>">
                    <div id="edit-menu-preview-icon-<?php echo $item_id; ?>" class="edit-menu-preview-icon"><?php echo $preview; ?></div>
                    <div id="edit-menu-button-icon-<?php echo $item_id; ?>" class="edit-menu-button-icon button button-primary button-large" data-id="<?php echo $item_id; ?>"><?php _e('Choose Icon','mctwsi'); ?></div>
                    <div id="edit-menu-remove-icon-<?php echo $item_id; ?>" class="edit-menu-remove-icon button button-large" data-id="<?php echo $item_id; ?>"><?php _e('Remove','mctwsi'); ?></div>

                    <input type="hidden" value="<?php echo $value; ?>" id="edit-menu-item-icon_<?php echo $item_id; ?>" name="edit-menu-item-icon[<?php echo $item_id; ?>]" />
                </label>
            </p>
        </div>
        <!-- /Menu Icon item -->


        <!-- Mega Menu Footer item -->
        <?php
        $value = get_post_meta( $item->ID, '_menu_item_footer', true); ?>
        <div class="clearboth"></div>
        <div class="footer-menu-container">
            <p class="field-link-footer">
                <label for="edit-menu-item-footer-<?php echo $item_id; ?>">
                    <?php _e( 'Mega menu footer content', 'mctwsi' ); ?><br />
                    <textarea id="edit-menu-item-footer-<?php echo $item_id; ?>" name="edit-menu-item-footer[<?php echo $item_id; ?>]"><?php echo $value; ?></textarea>
                </label>
            </p>
        </div>
        <!-- /Mega Menu Footer -->

        <div class="menu-item-actions description-wide submitbox">
            <?php if( 'custom' != $item->type && $original_title !== false ) : ?>
                <p class="link-to-original">
                    <?php printf( __('Original: %s','mctwsi'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
                </p>
            <?php endif; ?>
            <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
            echo wp_nonce_url(
                add_query_arg(
                    array(
                        'action' => 'delete-menu-item',
                        'menu-item' => $item_id,
                    ),
                    admin_url( 'nav-menus.php' )
                ),
                'delete-menu_item_' . $item_id
            ); ?>"><?php _e( 'Remove','mctwsi'); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( array( 'edit-menu-item' => $item_id, 'cancel' => time() ), admin_url( 'nav-menus.php' ) ) );
            ?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel','mctwsi'); ?></a>
        </div>

        <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
        <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
        <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
        <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
        <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
        <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
    </div><!-- .menu-item-settings-->
    <ul class="menu-item-transport"></ul>
        <?php
        $output .= ob_get_clean();
    }
}

/**
 * Create HTML list of nav menu items
 *
 * @package WordPress
 * @since 3.0.0
 * @uses Walker
 */
class mc_twsi_walker_nav_menu extends Walker_Nav_Menu {

    /**
     * Blog menu parents, all child items must be skipped for blog menu items (from deppth = 0)
     * @var array
     */
    var $blog_menu_parents = array();

    /**
     * Starts the list before the elements are added.
     *
     * @see Walker::start_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     */
    function start_lvl( &$output, $depth = 0, $args = array() ) {

        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }

    /**
     * @see Walker::start_el()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Menu item data object.
     * @param int $depth Depth of menu item. Used for padding.
     * @param int $current_page Menu item ID.
     * @param object $args
     */
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

        //skip menu elements if blog menu is activated for parent element
        if ($depth > 0  && in_array($item -> menu_item_parent, $this -> blog_menu_parents)) {
            return;
        }

        if ($depth == 1 && $item -> header == 'enabled') {
            $item_output = '<li><span>'.apply_filters( 'the_title', $item->title, $item->ID ).'</span>';
            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        } else {
            $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

            $class_names = $value = '';

            $classes = empty( $item->classes ) ? array() : (array) $item->classes;
            $classes[] = 'menu-item-' . $item->ID;

            $icon = '';
            if (!empty($item -> icon)) {
                $icon = '<i class="'. $item -> icon.'"></i>';
            }

            $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
            $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

            $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
            $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

            $output .= $indent . '<li' . $id . $value . $class_names .'>';

            $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
            $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
            $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
            $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

            $item_output = $args->before;
            $item_output .= '<a'. $attributes .'>';
            $item_output .= $icon;
            $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;

            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

            if ($depth == 0 && $item -> megamenu == 'enabled') {
                $output .= '<div class="mega-menu">';
            }

        } //if header menu item
    }

    /**
     * @see Walker::end_el()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Page data object. Not used.
     * @param int $depth Depth of page. Not Used.
     */
    function end_el( &$output, $item, $depth = 0, $args = array() ) {

        //skip menu elements if blog menu is activated for parent element
        if ($depth > 0  && !in_array($item -> menu_item_parent, $this -> blog_menu_parents)) {
            return;
        }

        if ($depth == 0 && $item -> megamenu == 'enabled') {
            if (!empty($item -> footer)) {
                $output .= '<div class="mega-menu-footer">'.$item -> footer.'</div>';
            }
            $output .= '</div>';
        }


        $output .= "</li>\n";
    }
}

