<?php

namespace App\Widgets;

use Elementor\Widget_Base;

class Occupation_Scroll_Widget extends Widget_Base {
    public function get_name() {
        return 'occupation_scroll_widget';
    }

    public function get_title() {
        return esc_html__( 'Occupation Scroll', 'omakeikka-theme' );
    }

    public function get_icon() {
        return 'eicon-code';
    }

    public function get_categories() {
        return [ 'basic' ];
    }

    public function get_keywords() {
        return [ 'scroll', 'list', 'occupation' ];
    }

    protected function render() {
        echo view( 'widgets/occupation-scroll-widget' )->render();
    }
}
