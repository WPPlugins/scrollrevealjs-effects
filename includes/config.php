<?php

function scroll_reveal_js_options_page() {
    ?>
    <h2 class="wrap" style="margin-bottom: 30px;">ScrollReveal.js Effects Configuration</h2>
    <div class="metabox-holder postbox wrap">
        <form method="post" action="options.php" class="inside"> 
        <?php
            settings_fields( 'sr-option-group' );
            do_settings_sections( 'sr-option-group' );
            submit_button();
        ?>
        </form>
    </div>
    <?php
}

add_action( 'admin_init', 'register_scroll_reveal_js_settings' );

function register_scroll_reveal_js_settings() {
    register_setting( 'sr-option-group', 'use_sr_config' );
    register_setting( 'sr-option-group', 'sr_config' );
    add_settings_section('sr_main', 'SR.js Settings', 'sr_section_text', 'sr-option-group');
    add_settings_section('src_main', 'Configuration Settings', 'src_section_text', 'sr-option-group');
    add_settings_field(
            'sr_text_string', 
            'Use configuration', 
            'sr_setting_string',
            'sr-option-group', 
            'sr_main'
        );
    
    add_settings_field(
            'src_opacity', 
            'Opacity', 
            'sr_configuration_opacity',
            'sr-option-group', 
            'src_main'
        );
    
    add_settings_field(
            'src_mobile', 
            'Mobile', 
            'sr_configuration_mobile',
            'sr-option-group', 
            'src_main'
        );
    
    add_settings_field(
            'src_reset', 
            'Reset', 
            'sr_configuration_reset',
            'sr-option-group', 
            'src_main'
        );
    
    add_settings_field(
            'src_viewport', 
            'Viewport', 
            'sr_configuration_viewport',
            'sr-option-group', 
            'src_main'
        );
    
    add_settings_field(
            'src_delay', 
            'Delay', 
            'sr_configuration_delay',
            'sr-option-group', 
            'src_main'
        );
    
    add_settings_field(
            'src_vFactor', 
            'vFactor', 
            'sr_configuration_vFactor',
            'sr-option-group', 
            'src_main'
        );
}

function sr_section_text() {
    echo '<p>Choose your configuration of ScrollReveal.js Plugin</p>';
}

function src_section_text() {
    echo '<p>Set configuration of ScrollReveal.js Plugin</p>';
}

function sr_setting_string() {
    $options = get_option('use_sr_config'); ?>
    <input type="checkbox" name="use_sr_config[use]" value="1"<?php checked( isset( $options['use'] ) ); ?> />
    <?php
}

function sr_configuration_opacity() {
    $options = get_option('sr_config'); ?>
    <input id="src-opacity" type="text" name="sr_config[opacity]" value="<?php echo $options['opacity']; ?>" placeholder="Opacity ratio in float"/>
    <?php
}

function sr_configuration_mobile() {
    $options = get_option('sr_config'); ?>
    <input id="src-mobile" type="checkbox" name="sr_config[mobile]" value="1"<?php checked( isset( $options['mobile'] ) ); ?> />
    <?php
}

function sr_configuration_reset() {
    $options = get_option('sr_config'); ?>
    <input type="checkbox" name="sr_config[reset]" value="1"<?php checked( isset( $options['reset'] ) ); ?> />
    <?php
}

function sr_configuration_viewport() {
    $options = get_option('sr_config'); ?>
    <input type="text" name="sr_config[viewport]" value="<?php echo $options['viewport']; ?>" placeholder="#ID of viewport element"/>
    <?php
}

function sr_configuration_delay() {
    $options = get_option('sr_config');
    $select_options = array('once','always'); ?>
    <select name="sr_config[delay]">
        <?php
        foreach ($select_options as $s) {
            if($options['delay'] == $s) {
                echo '<option value="'.$s.'" selected>'.$s.'</option>';
            } else {
                echo '<option value="'.$s.'">'.$s.'</option>';
            }
        }
        ?>
    </select>
    <?php
}

function sr_configuration_vFactor() {
    $options = get_option('sr_config'); ?>
    <input id="src-opacity" type="text" name="sr_config[vFactor]" value="<?php echo $options['vFactor']; ?>" placeholder="Element ratio in float" />
    <?php
}