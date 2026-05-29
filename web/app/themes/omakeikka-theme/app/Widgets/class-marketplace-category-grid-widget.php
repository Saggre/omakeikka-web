<?php

namespace App\Widgets;

use App\Services\MarketplaceCategoryQuery;
use Elementor\Widget_Base;

class Marketplace_Category_Grid_Widget extends Widget_Base {

    public function get_name() {
        return 'marketplace_category_grid_widget';
    }

    public function get_title() {
        return esc_html__( 'Marketplace Category Grid', 'omakeikka-theme' );
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return array( 'basic' );
    }

    public function get_keywords() {
        return array( 'marketplace', 'palvelut', 'palvelutori', 'categories' );
    }

    protected function render() {
        echo view( 'widgets/marketplace-category-grid-widget', array(
            'groups' => MarketplaceCategoryQuery::groups(),
        ) )->render();
    }
}
