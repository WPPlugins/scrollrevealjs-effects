<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

global $wpdb;
$table_name = $wpdb->prefix . 'scrollrevealjs';
$pages = get_all_page_ids();
$current_page = get_the_ID();

if ( in_array( $current_page, $pages ) ) {
    $items = $wpdb->get_results("SELECT * FROM $table_name WHERE page_id = $current_page");
} else {
    $items = $wpdb->get_results("SELECT * FROM $table_name");
}

?>
<script type="text/javascript">
    <?php    
    foreach ($items as $item) {
        $param = '';
        
        if(!empty($item->wait)) { $param .= 'wait '.$item->wait.'s, '; }
        if(!empty($item->enter)) { $param .= 'enter '.$item->enter.', '; }
        if(!empty($item->move)) { $param .= 'move '.$item->move.'px, '; }
        if(!empty($item->flip)) { $param .= 'flip '.$item->flip.'deg, '; }
        if(!empty($item->spin)) { $param .= 'spin '.$item->spin.'deg, '; }
        if(!empty($item->roll)) { $param .= 'roll '.$item->roll.'deg, '; }
        if(!empty($item->scale)) { $param .= 'scale '.$item->scale.'%, '; }
        if(!empty($item->over)) { $param .= 'over '.$item->over.'s, '; }
        if(!empty($item->reset)) { $param .= $item->reset; }
        
        $param = rtrim($param,", ");
        
        echo 'jQuery("#'.$item->selector.'").attr("data-sr","'.$param.'");'."\n\t";
    }
    ?>
</script>