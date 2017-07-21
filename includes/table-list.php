<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Scroll_Reveal_List_Table extends WP_List_Table
{
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'effect',
            'plural' => 'effects',
            'ajax' => true
        ));
    }
    
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }
    
    function column_selector($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
        $actions = array(
            'edit' => sprintf('<a href="?page=scroll_reveal_js_form&id=%s">%s</a>', $item['id'], __('Edit', 'scroll_reveal_js')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'scroll_reveal_js')),
        );

        return sprintf('#%s %s',
            $item['selector'],
            $this->row_actions($actions)
        );
    }
    
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }
    
    function column_params($item)
    {
        $param = '';
        
        if( !empty( $item['wait'] ) ) { $param .= 'wait '.$item['wait'].'s, '; }
        if( !empty( $item['enter'] ) ) { $param .= 'enter '.$item['enter'].', '; }
        if( !empty( $item['move'] ) ) { $param .= 'move '.$item['move'].'px, '; }
        if( !empty( $item['flip'] ) ) { $param .= 'flip '.$item['flip'].'deg, '; }
        if( !empty( $item['spin'] ) ) { $param .= 'spin '.$item['spin'].'deg, '; }
        if( !empty( $item['roll'] ) ) { $param .= 'roll '.$item['roll'].'deg, '; }
        if( !empty( $item['scale'] ) ) { $param .= 'scale '.$item['scale'].'%, '; }
        if( !empty( $item['over'] ) ) { $param .= 'over '.$item['over'].'s, '; }
        if( !empty( $item['reset'] ) ) { $param .= $item['reset']; }
        
        return rtrim($param,", ");
    }
    
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'selector' => __('Selector', 'scroll_reveal_js'),
            'params' => __('Parameters', 'scroll_reveal_js')
        );
        return $columns;
    }
    
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }    
    
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'scrollrevealjs'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }
    
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'selector' => array('name', true),
        );
        return $sortable_columns;
    }
    
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'scrollrevealjs'; // do not forget about tables prefix

        $per_page = 5; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'selector';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        
        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
        $page_filter = ( isset( $_REQUEST['page-filter'] ) ) ? $_REQUEST['page-filter'] : false;
        
        if ( $page_filter and $search ){
            printf('mam pagefiltr');
            $search = trim($search);
            // Trim Search Term
            $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE page_id = %d AND selector LIKE '%%%s%%' ORDER BY $orderby $order LIMIT %d OFFSET %d", $page_filter, $search, $per_page, $paged), ARRAY_A);
        } elseif ( $search and !$page_filter ){
            // Trim Search Term
            $search = trim($search);
            /* Notice how you can search multiple columns for your search term easily, and return one data set */
            $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE selector LIKE '%%%s%%' ORDER BY $orderby $order LIMIT %d OFFSET %d", $search, $per_page, $paged), ARRAY_A);
        } elseif ( !$search and $page_filter ){
            $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE page_id = %d ORDER BY $orderby $order LIMIT %d OFFSET %d", $page_filter, $per_page, $paged), ARRAY_A);
        } else {
            $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
        }

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
    
    function extra_tablenav( $which ) {
        global $wpdb, $testiURL, $tablet;
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'scrollrevealjs';
        
        if ( $which == "top" ){
            ?>
            <div class="alignleft actions bulkactions">
            <?php
            $pages = $wpdb->get_results('select distinct page_id from '.$table_name, ARRAY_A);
            if( $pages ){
                ?>
                <select name="page-filter" onchange="this.form.submit()">
                    <option value="">Filter by Page</option>
                    <?php
                        foreach ($pages as $page) {
                            if( $_GET['page-filter'] == $page['page_id'] ){
                                echo '<option value="'.$page['page_id'].'" selected>'.get_the_title($page['page_id']).'</option>';
                            } else {
                                echo '<option value="'.$page['page_id'].'">'.get_the_title($page['page_id']).'</option>';
                            }
                        }
                    ?>
                </select>
                <?php   
            }
            ?>  
            </div>
            <?php
        }
        if ( $which == "bottom" ){
            //The code that goes after the table is there

        }
    }
}