<?php

namespace App\Services\HelpRequest;

/**
 * Triage status of a help request. Any status may move to any other.
 */
enum Status: string
{
    case New       = 'uusi';
    case Contacted = 'yhteydessa';
    case Agreed    = 'sovittu';
    case Done      = 'valmis';
    case Cancelled = 'peruttu';

    public function label(): string {
        return match ( $this ) {
            self::New       => __( 'New', 'omakeikka-theme' ),
            self::Contacted => __( 'In contact', 'omakeikka-theme' ),
            self::Agreed    => __( 'Agreed', 'omakeikka-theme' ),
            self::Done      => __( 'Done', 'omakeikka-theme' ),
            self::Cancelled => __( 'Cancelled', 'omakeikka-theme' ),
        };
    }
}
