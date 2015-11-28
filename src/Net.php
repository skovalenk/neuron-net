<?php
namespace Sereban\NeuronNet;

use Sereban\NeuronNet\Layout;

final class Net
{
    const TEACHER_MODE    = 1;
    const PRODUCTION_MODE = 2;
    //DIRECTIONS
    const DIRECT  = 3; //for values
    const REVERSE = 8; //for mistakes
    //Allowed circles of mistakes
    const DEFAULT_ALLOWED_ITERATIONS   = 1000;
    /** @var int  */
    private static $_iterations = 0;
    private static $_direction = self::DIRECT;
    /** @var int -> We are using production mode by default */
    private static $_mode    = self::PRODUCTION_MODE;

    private static $_reloadValuesVia = 5;
    /**
     * @var bool -> Enable debug info
     */
    private static $_debugOn = false;
    private static $_allowedIterations;
    /**
     * Return Current direction of NN
     * @return int
     */
    public static function getDirection() {
        return self::$_direction;
    }

    private function __construct() {
        //Only singleton
    }

    /**
     * @param $debugOn
     */
    public static function setDebugFlag($debugOn) {
        self::$_debugOn = $debugOn;
    }

    /**
     * Set Count of iterations, using the count of which we should set new values to neuron network
     * @param $count
     */
    public static function setReloadValuesVia($count) {
        self::$_reloadValuesVia = $count;
    }

    /**
     * @return bool
     */
    public static function isDebugEnabled() {
        return self::$_debugOn;
    }

    /**
     * @return bool
     */
    public static function isTeacherMode() {
        return self::TEACHER_MODE == self::$_mode;
    }

    /**
     * @param $_mode
     */
    public static function setMode($_mode) {
        if($_mode == self::PRODUCTION_MODE) {
            self::$_direction  = self::DIRECT;
            self::$_iterations = 0;
        }
        self::$_mode = $_mode;
    }

    public static function incrementIteration($number = 1) {
        self::$_iterations += $number;
    }

    /**
     * @param $count
     */
    public static function setIterationsCount($count) {
        self::$_iterations = $count;
    }

    /**
     * @return bool
     */
    public static function shouldReloadValues() {
        return self::$_iterations % self::$_reloadValuesVia == 0;
    }

    /**
     * In Production mode we have only one iteration
     * @return int
     */
    protected static function _getAllowedIterations() {
        $_defaultAllowedIterations = self::$_allowedIterations ? self::$_allowedIterations : self::DEFAULT_ALLOWED_ITERATIONS;
        return (self::isTeacherMode()) ? $_defaultAllowedIterations : 1;
    }

    public static function setAllowedIterationsCount($iterations) {
        self::$_allowedIterations = $iterations;
    }

    /**
     * @return bool
     */
    public static function validateIteration() {
        return self::$_iterations < self::_getAllowedIterations();
    }
    /**
     * @return bool
     */
    public static function isMistakeFlow() {
        return self::isTeacherMode() && self::$_direction == self::REVERSE;
    }

    /**
     * @return int
     */
    public static function getLayoutCounter() {
        return (self::isMistakeFlow()) ? -1 : 1;
    }

    /**
     * @param $direction
     */
    public static function setDirection($direction) {
        self::$_direction = $direction;
    }
}