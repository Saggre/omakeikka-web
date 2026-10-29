<?php

namespace App\Providers;

use App\Services\HelpRequest\Admin;
use App\Services\HelpRequest\Form;
use App\Services\HelpRequest\PostType;
use App\Services\HelpRequest\Privacy;
use App\Services\HelpRequest\Repository;
use App\Services\HouseholdDeduction;
use App\Services\Municipalities;
use Illuminate\Support\ServiceProvider;

use function Roots\view;

/**
 * Wires the household help request services to WordPress hooks.
 */
class HelpRequestServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton( Municipalities::class );
        $this->app->singleton( HouseholdDeduction::class );
        $this->app->singleton( Repository::class );
    }

    public function boot(): void
    {
        $post_type = $this->app->make( PostType::class );
        $privacy   = $this->app->make( Privacy::class );
        $admin     = $this->app->make( Admin::class );
        $form      = $this->app->make( Form::class );

        add_action( 'init', array( $post_type, 'register' ) );
        add_action( 'init', array( $privacy, 'schedule' ) );
        add_action( Privacy::CRON_HOOK, array( $privacy, 'cleanup' ) );
        add_filter( 'wp_privacy_personal_data_exporters', array( $privacy, 'registerExporter' ) );
        add_filter( 'wp_privacy_personal_data_erasers', array( $privacy, 'registerEraser' ) );

        add_filter( 'manage_' . Repository::POST_TYPE . '_posts_columns', array( $admin, 'columns' ) );
        add_action( 'manage_' . Repository::POST_TYPE . '_posts_custom_column', array( $admin, 'renderColumn' ), 10, 2 );
        add_action( 'add_meta_boxes_' . Repository::POST_TYPE, array( $admin, 'addMetaBoxes' ) );
        add_action( 'save_post_' . Repository::POST_TYPE, array( $admin, 'saveStatus' ) );

        // Without the logged in variant an admin testing the form gets a blank admin-post response.
        add_action( 'admin_post_' . Form::ACTION, array( $form, 'handle' ) );
        add_action( 'admin_post_nopriv_' . Form::ACTION, array( $form, 'handle' ) );

        // The homepage is Elementor content, so the section is placed with the Shortcode widget.
        add_shortcode( 'omakeikka-kuka-voi-ostaa', fn() => view( 'partials.who-can-buy' )->render() );
    }
}
