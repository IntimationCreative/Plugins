<?php

require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';
class Quiet_Skin extends \WP_Upgrader_Skin {
    public function feedback($string)
    {
        // just keep it quiet
    }
}