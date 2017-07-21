<?php
/* 
Plugin Name: ScrollReveal.js Effects
Plugin URI: http://sr.cekuj.net
Description: Use and manage scroll reveal effects using ScrollReveal.js right in WordPress
Version: 1.2
Author: Jan Benedík
Author URI: https://cz.linkedin.com/pub/jan-benedik/a0/84b/636
Tags: ScrollReveal.js, scroll, reveal, effects
License: GNU General Public License v2 or later
 
    Copyright 2015  Jan Benedík (email: benedikj.jan@gmal.com)
 
    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License,
    version 2, as published by the Free Software Foundation. 
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of 
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
    GNU General Public License for more details. 
 
    You should have received a copy of the GNU General Public License 
    along with this program; if not, write to the Free Software 
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 
    02110-1301  USA 
*/

// INCLUDES

require_once dirname( __FILE__ ) . '/includes/config.php';

require_once dirname( __FILE__ ) . '/includes/table-list.php';

require_once dirname( __FILE__ ) . '/includes/edit.php';

require_once dirname(__FILE__) . '/includes/simple_html_dom.php';

global $scroll_reveal_js_db_version;
$scroll_reveal_js_db_version = '1.0';

function scroll_reveal_js_table_install()
{
    global $wpdb;
    global $scroll_reveal_js_db_version;

    $table_name = $wpdb->prefix . 'scrollrevealjs';

    $sql = "CREATE TABLE " . $table_name . " (
      id int(11) NOT NULL AUTO_INCREMENT,
      page_id int(11) NOT NULL,
      selector VARCHAR(500),
      enter VARCHAR(500) NULL,
      move VARCHAR(500) NULL,
      over VARCHAR(500) NULL,
      wait VARCHAR(500) NULL,
      flip VARCHAR(500) NULL,
      spin VARCHAR(500) NULL,
      roll VARCHAR(500) NULL,
      scale VARCHAR(500) NULL,
      reset VARCHAR(500) NULL,
      PRIMARY KEY  (id)
    );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    add_option('scroll_reveal_js_db_version', $scroll_reveal_js_db_version);
}

function scroll_reveal_js_table_install_data()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'scrollrevealjs';
    
    $wpdb->insert($table_name, array(
        'page_id' => 0,
        'selector' => 'test',
        'enter' => 'left',
        'move' => '200',
        'over' => '4'
    ));
}

function scroll_reveal_js_update_db_check()
{
    global $scroll_reveal_js_db_version;
    if (get_site_option('scroll_reveal_js_db_version') != $scroll_reveal_js_db_version) {
        scroll_reveal_js_table_install();
    }
}

// HOOKS

register_activation_hook(__FILE__, 'scroll_reveal_js_table_install');

register_activation_hook(__FILE__, 'scroll_reveal_js_table_install_data');

add_action('plugins_loaded', 'scroll_reveal_js_update_db_check');

function load_sr_js()
{
    wp_register_script( 'scroll-reveal-js', plugins_url( '/js/scrollReveal.min.js', __FILE__ ), array( 'jquery' ), '', true );
    wp_enqueue_script('scroll-reveal-js');
}

function sr_footer() {
    require dirname( __FILE__ ) . '/templates/footer.php';
}

function sr_init() {
    require dirname( __FILE__ ) . '/templates/sr-init.php';
}

add_action('wp_enqueue_scripts','load_sr_js');

add_action('wp_footer','sr_footer');

add_action( 'wp_footer', 'sr_init', 101 );

add_action( 'admin_menu', 'scroll_revela_js_menu' );

if( !function_exists("scroll_revela_js_menu") )
{
    function scroll_revela_js_menu(){

        $page_title = 'ScrollReveal.js Plugin';
        $menu_title = 'ScrollReveal.js';
        $capability = 'activate_plugins';
        $menu_slug  = 'scroll_reveal_js';
        $function   = 'scroll_reveal_js_page';
        $icon_url   = plugins_url( 'scroll-reveal-js/icon.png' );
        $position   = 64;

        add_menu_page(
            $page_title, 
            $menu_title, 
            $capability, 
            $menu_slug, 
            $function, 
            $icon_url, 
            $position
        );
        
        add_submenu_page('scroll_reveal_js', 
            'Used effects', 
            'Used effects', 
            'activate_plugins', 
            'scroll_reveal_js', 
            'scroll_reveal_js_page'
        );
        
        add_submenu_page('scroll_reveal_js', 
            'Add new effect', 
            'Add new effect', 
            'activate_plugins', 
            'scroll_reveal_js_form', 
            'scroll_reveal_js_form_page'
        );
        
        add_submenu_page('scroll_reveal_js', 
            'Configuration', 
            'Configuration', 
            'manage_options', 
            'scroll_reveal_js_options', 
            'scroll_reveal_js_options_page'
        );
    }
}

if( !function_exists("scroll_reveal_js_page") )
{
    function scroll_reveal_js_page(){
        ?>
        <h1>ScrollReveal.js Effects</h1>
        <?php
        
        global $wpdb;

        $table = new Scroll_Reveal_List_Table();
        $table->prepare_items();
        
        $message = '';
        if ('delete' === $table->current_action()) {
            $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'custom_table_example'), count($_REQUEST['id'])) . '</p></div>';
        }?>
        
        <div class="wrap">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
            <h2><a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=scroll_reveal_js_form');?>"><?php _e('Add new', 'custom_table_example')?></a>
            </h2>
            <?php echo $message; ?>

            <form id="effects-table" method="GET">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
                <?php
                    $table->search_box('Search Selector', 'search_selector');
                    $table->display()
                ?>
            </form>
        </div>
        <script>
            jQuery(function($){
                $('#search_selector-search-input').on('input change', function() {
                    if (!$('#search_selector-search-input').val()) {
                        $(this).parents('form').submit();
                    }
                });
            });
        </script>
        <?php
    }
}

?>