<?php
/**
 * Class Narrator
 *
 * Provides a simple log of processing activity.
 * Uses echo when it's safe to do so.
 * @TODO Add this test.
 *
 * @package oik-i18n
 * @copyright (C) Copyright Bobbing Wide 2020
 */
class Narrator {

    private $nested = 0;

    /**
     * @var Narrator the true instance
     */
    private static $instance;

    /**
     * Return a single instance of this class
     *
     * @return object
     */
    public static function instance() {
        if ( !isset( self::$instance ) && !( self::$instance instanceof self ) ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Narrates the activity.
     *
     * Primarily for debug purposes.
     *
     * May end up using echo if it's available.
     *
     * @param $label
     * @param $value
     */
    function narrate( $label, $value ) {
        $narration = $this->nesting();
        $narration .= $label;
        $narration .= ": ";
        $narration .= $value;
        $narration .=  PHP_EOL;
        echo $narration;
    }

    function nesting()  {
        return str_repeat( '   ', $this->nested );
    }

    function nest() {
        $this->nested++;
    }

    function denest() {
        $this->nested--;
    }


}