<?php

namespace App\Widgets;

use Elementor\Widget_Base;

class Municipality_Map_Widget extends Widget_Base {
    public function get_name() {
        return 'municipality_map_widget';
    }

    public function get_title() {
        return esc_html__( 'Municipality Map', 'omakeikka-wp-theme' );
    }

    public function get_icon() {
        return 'eicon-code';
    }

    public function get_categories() {
        return [ 'basic' ];
    }

    public function get_keywords() {
        return [ 'map', 'municipality' ];
    }

    protected function render() {
        echo view( 'widgets/municipality-map-widget' )->render();
    }
}
