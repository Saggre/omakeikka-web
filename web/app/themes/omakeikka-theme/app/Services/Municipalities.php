<?php

namespace App\Services;

/**
 * Reads the bundled Finnish municipality list.
 *
 * The source of truth is resources/data/municipalities_full.json, which holds
 * all 309 municipalities keyed by their zero-padded three character Statistics
 * Finland code. Note that resources/data/municipalities.json is the inverse
 * map, name to code, and is NOT the file to use here. The omakeikka Laravel
 * app carries its own copy in the opposite direction again, so do not assume
 * the two are interchangeable.
 *
 * The code is always handled as a string. Casting "005" to an int loses the
 * padding and breaks the join against the app's char(4) municipality_id.
 */
class Municipalities
{
    /**
     * Whole decoded file, memoised per request.
     *
     * @return array<string, array{municipality_id:string, title:array{fi:string, se:string}}>
     */
    public static function all(): array {
        static $cache = null;

        if ( null !== $cache ) {
            return $cache;
        }

        $file = get_theme_file_path( 'resources/data/municipalities_full.json' );
        $raw  = is_readable( $file ) ? file_get_contents( $file ) : '';
        $data = json_decode( $raw, true );

        $cache = is_array( $data ) ? $data : array();

        return $cache;
    }

    /**
     * Municipality code to Finnish name, sorted the way a Finn expects.
     *
     * A plain asort() puts Å, Ä and Ö after Z. Collator with fi_FI does not.
     * The intl extension is installed in .docker/php/Dockerfile, but fall back
     * anyway so a misconfigured environment degrades rather than fatals.
     *
     * @return array<string, string>
     */
    public static function options(): array {
        static $cache = null;

        if ( null !== $cache ) {
            return $cache;
        }

        $options = array();

        foreach ( self::all() as $code => $municipality ) {
            $name = $municipality['title']['fi'] ?? '';

            if ( '' !== $name ) {
                $options[ (string) $code ] = $name;
            }
        }

        if ( class_exists( '\Collator' ) ) {
            collator_asort( new \Collator( 'fi_FI' ), $options );
        } else {
            asort( $options, SORT_LOCALE_STRING );
        }

        $cache = $options;

        return $cache;
    }

    /**
     * True when the code exists in the bundled list.
     */
    public static function isValid( string $code ): bool {
        return '' !== $code && array_key_exists( $code, self::all() );
    }

    /**
     * Finnish name for a code, or an empty string when unknown.
     */
    public static function name( string $code ): string {
        if ( '' === $code ) {
            return '';
        }

        return self::all()[ $code ]['title']['fi'] ?? '';
    }
}
