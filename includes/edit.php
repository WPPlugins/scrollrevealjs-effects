<?php

if( !function_exists("scroll_reveal_js_form_page") )
{
    function scroll_reveal_js_form_page(){
        ?>
        <h1>ScrollReveal.js Effects</h1>
        <?php
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'scrollrevealjs';

        $message = '';
        $notice = '';

        $default = array(
            'id' => 0,
            'page_id' => 0,
            'selector' => '',
            'enter' => '',
            'move' => '',
            'over' => '',
            'wait' => '',
            'flip' => '',
            'spin' => '',
            'roll' => '',
            'scale' => '',
            'reset' => '',
        );
        
        if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
            $item = shortcode_atts($default, $_REQUEST);
            $item_valid = scroll_reveal_js_validate_effect($item);
            if ($item_valid === true) {
                if ($item['id'] == 0) {
                    $result = $wpdb->insert($table_name, $item);
                    $item['id'] = $wpdb->insert_id;
                    if ($result) {
                        $message = __('Item was successfully saved', 'scroll_reveal_js');
                    } else {
                        $notice = __('There was an error while saving item', 'scroll_reveal_js');
                    }
                } else {
                    $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                    if ($result) {
                        $message = __('Item was successfully updated', 'scroll_reveal_js');
                    } else {
                        $notice = __('There was an error while updating item', 'scroll_reveal_js');
                    }
                }
            } else {
                $notice = $item_valid;
            }
        }
        else {
            $item = $default;
            if (isset($_REQUEST['id'])) {
                $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
                if (!$item) {
                    $item = $default;
                    $notice = __('Item not found', 'scroll_reveal_js');
                }
            }
        }
        
        add_meta_box('effects_form_meta_box', 'Effect details', 'scroll_reveal_js_form_meta_box_handler', 'scroll_reveal_js', 'normal', 'default');
        
        $pages = get_all_page_ids();
        
        global $page_id;
        
        if(isset($_POST['selectPage']) or $item['page_id'] > 0) {
            
            if(isset($_POST['selectPage'])) {
                $page_id = $_POST['selectPage'];
            }
            
            if($item['page_id'] > 0) {
                $page_id = $item['page_id'];
            }
        } else {
            $page_id = $pages[0];
        }
        
        ?>
        
        <div class="metabox-holder postbox wrap">
            <form action="" method="POST" class="inside">
                <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
                    <tbody>
                        <tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="selectPage">Choose page</label>
                            </th>
                            <td>
                                <select id="selectPage" name="selectPage" onchange="this.form.submit()" <?php if($item['page_id'] > 0) {echo 'disabled';} ?>>
                                    <?php
                                        foreach ($pages as $page) {
                                            if($page_id == $page) {
                                                echo '<option value="'.$page.'" selected>'.get_the_title($page).'</option>';
                                            } else {
                                                echo '<option value="'.$page.'">'.get_the_title($page).'</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        
        <div class="wrap">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
            <h2>
                <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=scroll_reveal_js');?>"><?php _e('back to list', 'scroll_reveal_js')?></a>
            </h2>

            <?php if (!empty($notice)): ?>
            <div id="notice" class="error"><p><?php echo $notice ?></p></div>
            <?php endif;?>
            <?php if (!empty($message)): ?>
            <div id="message" class="updated"><p><?php echo $message ?></p></div>
            <?php endif;?>

            <form id="form" method="POST">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
                <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

                <div class="metabox-holder" id="poststuff">
                    <div id="post-body">
                        <div id="post-body-content">
                            <?php do_meta_boxes('scroll_reveal_js', 'normal', $item); ?>
                            <input type="submit" value="<?php _e('Save','scroll-reveal-js') ?>" id="submit" class="button-primary" name="submit">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}

function scroll_reveal_js_form_meta_box_handler($item)
{
    global $page_id;

    $url = get_page_link($page_id);

    $dom = file_get_html($url);
    $divs = $dom->find('[id]');
    $sorted = array();
    foreach ($divs as $div) {
        array_push($sorted, $div->id);
    }
    sort($sorted, SORT_STRING | SORT_FLAG_CASE);
    ?>

    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
        <tbody>
        <input type="hidden" name="page_id" value="<?php echo esc_attr($page_id)?>"/>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="selector">Selector</label>
            </th>
            <td>
                <select id="selector" name="selector" style="width: 85%">
                    <?php
                        foreach ($sorted as $div) {
                            if($item['selector'] == $div) {
                                echo '<option value="'.$div.'" selected>'.$div.'</option>';
                            } else {
                                echo '<option value="'.$div.'">'.$div.'</option>';
                            }
                        }
                    ?>
                </select>
            </td>
            <th valign="top" scope="row">
                <label for="enter">Enter</label>
            </th>
            <td>
                <select id="enter" name="enter" style="width: 85%">
                    <?php
                        $ways = array('left','top','right','bottom');
                        foreach ($ways as $way) {
                            if($item['enter'] == $way) {
                                echo '<option value="'.$way.'" selected>'.$way.'</option>';
                            } else {
                                echo '<option value="'.$way.'">'.$way.'</option>';
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="move">Move</label>
            </th>
            <td>
                <input id="move" name="move" type="text" style="width: 85%" value="<?php echo esc_attr($item['move'])?>"
                       size="50" class="code" placeholder="Length of move">
            </td>
            <th valign="top" scope="row">
                <label for="over">Over</label>
            </th>
            <td>
                <input id="over" name="over" type="text" style="width: 85%" value="<?php echo esc_attr($item['over'])?>"
                       size="50" class="code" placeholder="Time interval in seconds">
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="wait">Wait</label>
            </th>
            <td>
                <input id="wait" name="wait" type="text" style="width: 85%" value="<?php echo esc_attr($item['wait'])?>"
                       size="50" class="code" placeholder="Time of delay in seconds">
            </td>
            <th valign="top" scope="row">
                <label for="flip">Flip</label>
            </th>
            <td>
                <input id="flip" name="flip" type="text" style="width: 85%" value="<?php echo esc_attr($item['flip'])?>"
                       size="50" class="code" placeholder="Horizontal rotation in degrees">
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="spin">Spin</label>
            </th>
            <td>
                <input id="spin" name="spin" type="text" style="width: 85%" value="<?php echo esc_attr($item['spin'])?>"
                       size="50" class="code" placeholder="Vertical rotation in degrees">
            </td>
            <th valign="top" scope="row">
                <label for="roll">Roll</label>
            </th>
            <td>
                <input id="roll" name="roll" type="text" style="width: 85%" value="<?php echo esc_attr($item['roll'])?>"
                       size="50" class="code" placeholder="Rolling in degrees">
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="scale">Scale</label>
            </th>
            <td>
                <input id="scale" name="scale" type="text" style="width: 85%" value="<?php echo esc_attr($item['scale'])?>"
                       size="50" class="code" placeholder='Type "up 100" or "down 100"'> <!--"up/down degrees" of ratio-->
            </td>
            <th valign="top" scope="row">
                <label for="reset">Reset</label>
            </th>
            <td>
                <input type="checkbox" id="reset" name="reset" value="reset"<?php checked( !empty( $item['reset'] ) ); ?>>
            </td>
        </tr>
        </tbody>
    </table>
<?php
}

function scroll_reveal_js_validate_effect($item)
{
    $messages = array();

    if ( !empty($item['move']) && !is_numeric($item['move']) ) $messages[] = __('Move must be number', 'scroll-reveal-js');
    if ( !empty($item['over']) && !is_numeric($item['over']) ) $messages[] = __('Over must be number', 'scroll-reveal-js');
    if ( !empty($item['wait']) && !is_numeric($item['wait']) ) $messages[] = __('Wait must be number', 'scroll-reveal-js');
    if ( !empty($item['flip']) && !is_numeric($item['flip']) ) $messages[] = __('Flip must be number', 'scroll-reveal-js');
    if ( !empty($item['spin']) && !is_numeric($item['spin']) ) $messages[] = __('Spin must be number', 'scroll-reveal-js');
    if ( !empty($item['roll']) && !is_numeric($item['roll']) ) $messages[] = __('Roll must be number', 'scroll-reveal-js');
    //if (!empty($item['scale']) && !is_numeric($item['scale'])) $messages[] = __('Scale must be number', 'scroll-reveal-js');
    
    $scale = $item['scale'];
    
    if ( !empty($scale) ) {
        if(substr($scale,0,3) === "up ") {
            $rest = substr($scale,3);
            if(!is_numeric($rest)) {
                $messages[] = __('Number is missing, type as like "up 100"', 'scroll-reveal-js');
            }
        } elseif(substr($scale,0,5) === "down ") {
            $rest = substr($scale,5);
            if(!is_numeric($rest)) {
                $messages[] = __('Number is missing, type as like "down 100"', 'scroll-reveal-js');
            }
        } else {
            $messages[] = __('Scale must start with "up" or "down" followed by space', 'scroll-reveal-js');
        }
    }
    
    if (empty($messages)) return true;
    return implode('<br />', $messages);
}