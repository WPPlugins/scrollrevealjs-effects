<?php ?>

<script type="text/javascript">
    <?php 
        $options = get_option('use_sr_config');
        $sr_config = get_option('sr_config');
        
        if( isset( $options['use'] ) ) {
            $config = '';
            
            if( isset( $sr_config['opacity'] ) and !$sr_config['opacity'] == 0 ) {
                $config .= 'opacity: '.$sr_config['opacity'].', ';
            }
            
            if( isset( $sr_config['mobile'] ) ) {
                $config .= 'mobile: true, ';
            }
            
            if( isset( $sr_config['reset'] ) ) {
                $config .= 'reset: true, ';
            }
            
            if( isset( $sr_config['viewport'] ) and !$sr_config['viewport'] == 0 ) {
                $config .= 'viewport: document.getElementById("'.$sr_config['viewport'].'"), ';
            }
            
            if( isset( $sr_config['vFactor'] ) and !$sr_config['vFactor'] == 0 ) {
                $config .= 'vFactor: '.$sr_config['vFactor'].', ';
            }
            
            $config .= 'delay: "'.$sr_config['delay'].'", ';
            
            $config = rtrim($config,", ");
            
            if( strlen($config) > 0 ) {
                echo 'window.sr = new scrollReveal({ '. $config .' });'."\n";
            } else {
                echo 'window.sr = new scrollReveal();'."\n";
            }
        } else {
            echo 'window.sr = new scrollReveal();'."\n";
        }
    ?>
</script>
