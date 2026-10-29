<?php

namespace App\Services\HelpRequest;

/**
 * Editable starting text for the title and description, per service group.
 * A form that is already filled in converts better than an empty one, and the
 * text still reads as a valid request if the visitor sends it unchanged.
 */
final class Prefill
{
    /**
     * @return array{title: string, description: string} Empty strings for an unknown group.
     */
    public static function forGroup( string $group_slug ): array {
        [ $title, $need ] = match ( $group_slug ) {
            'piha'       => array( __( 'Yard work', 'omakeikka-theme' ), __( 'I am looking for someone to do yard work.', 'omakeikka-theme' ) ),
            'kodinhoito' => array( __( 'Cleaning and housekeeping', 'omakeikka-theme' ), __( 'I am looking for someone to clean and keep house.', 'omakeikka-theme' ) ),
            'kauppa'     => array( __( 'Shopping and errands', 'omakeikka-theme' ), __( 'I am looking for someone to do shopping and run errands.', 'omakeikka-theme' ) ),
            'muutto'     => array( __( 'Moving and carrying', 'omakeikka-theme' ), __( 'I am looking for someone to help me move or carry things.', 'omakeikka-theme' ) ),
            'hoiva'      => array( __( 'Care', 'omakeikka-theme' ), __( 'I am looking for someone to look after children, a pet or a loved one.', 'omakeikka-theme' ) ),
            'auto'       => array( __( 'Rides and car tasks', 'omakeikka-theme' ), __( 'I am looking for someone to give a ride or do a car task.', 'omakeikka-theme' ) ),
            'tekniikka'  => array( __( 'Computers and devices', 'omakeikka-theme' ), __( 'I am looking for someone who knows computers, phones or other devices.', 'omakeikka-theme' ) ),
            'asennukset' => array( __( 'Installation work', 'omakeikka-theme' ), __( 'I am looking for someone to do an installation at home.', 'omakeikka-theme' ) ),
            'remontti'   => array( __( 'Small renovation', 'omakeikka-theme' ), __( 'I am looking for someone to do a small renovation or repair.', 'omakeikka-theme' ) ),
            'ruoka'      => array( __( 'Cooking', 'omakeikka-theme' ), __( 'I am looking for someone to cook or prepare meals.', 'omakeikka-theme' ) ),
            'etaapu'     => array( __( 'Remote work', 'omakeikka-theme' ), __( 'I am looking for someone to work remotely, for example on writing or IT.', 'omakeikka-theme' ) ),
            'muu'        => array( __( 'Everyday tasks', 'omakeikka-theme' ), __( 'I am looking for someone to do everyday tasks.', 'omakeikka-theme' ) ),
            default      => array( '', '' ),
        };

        return array(
            'title'       => $title,
            'description' => '' !== $need ? $need . ' ' . __( 'I will tell you more about the details and a suitable time when we agree on the work.', 'omakeikka-theme' ) : '',
        );
    }
}
