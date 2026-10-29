<?php

namespace App\Services;

/**
 * Finnish municipalities from resources/data/municipalities_full.json, keyed
 * by the zero-padded Statistics Finland code. Codes stay strings, since "005"
 * as an int breaks the join against the app's char(4) municipality_id.
 */
final class Municipalities
{
    /** @var array<string, string>|null */
    private ?array $names = null;

    /**
     * Code to Finnish name, sorted so that Å, Ä and Ö come after Z.
     *
     * @return array<string, string>
     */
    public function options(): array {
        if ( null === $this->names ) {
            $data  = json_decode( (string) file_get_contents( get_theme_file_path( 'resources/data/municipalities_full.json' ) ), true );
            $names = array_map( fn( array $municipality ) => $municipality['title']['fi'], $data );

            collator_asort( new \Collator( 'fi_FI' ), $names );

            $this->names = $names;
        }

        return $this->names;
    }

    public function isValid( string $code ): bool {
        return '' !== $code && isset( $this->options()[ $code ] );
    }

    public function name( string $code ): string {
        return $this->options()[ $code ] ?? '';
    }
}
