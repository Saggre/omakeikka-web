<?php

namespace App\Services\HelpRequest;

enum Budget: string
{
    case Unknown  = 'ei-tiedossa';
    case Under100 = 'alle-100';
    case To300    = '100-300';
    case Over300  = 'yli-300';

    public function label(): string {
        return match ( $this ) {
            self::Unknown  => __( 'I do not know', 'omakeikka-theme' ),
            self::Under100 => __( 'Under 100 euros', 'omakeikka-theme' ),
            self::To300    => __( '100 to 300 euros', 'omakeikka-theme' ),
            self::Over300  => __( 'Over 300 euros', 'omakeikka-theme' ),
        };
    }
}
