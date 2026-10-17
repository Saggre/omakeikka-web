<?php

/**
 * Household help request form wiring.
 */

namespace App;

use App\Services\HelpRequestForm;
use App\Services\HouseholdDeduction;

/**
 * Both variants are needed. Without the non-nopriv one a logged in admin
 * testing the form gets a blank admin-post response.
 */
add_action( 'admin_post_nopriv_' . HelpRequestForm::ACTION, function () {
    HelpRequestForm::handle();
} );

add_action( 'admin_post_' . HelpRequestForm::ACTION, function () {
    HelpRequestForm::handle();
} );

/**
 * Warn an editor when the kotitalousvähennys eligibility mapping has not been
 * reviewed for over a year, so the list cannot silently rot.
 */
add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'manage_options' ) || ! HouseholdDeduction::isStale() ) {
        return;
    }

    $screen = get_current_screen();

    if ( ! $screen || ! in_array( $screen->post_type, array( 'marketplace_category', 'help_request' ), true ) ) {
        return;
    }

    printf(
        '<div class="notice notice-warning"><p>%s</p></div>',
        esc_html( sprintf(
            /* translators: %s is a date. */
            __( 'Kotitalousvähennyksen tiedot on tarkistettu viimeksi %s. Tarkista ajantasaisuus vero.fi:stä ja päivitä HouseholdDeduction::UPDATED.', 'omakeikka-theme' ),
            HouseholdDeduction::updatedLabel()
        ) )
    );
} );
