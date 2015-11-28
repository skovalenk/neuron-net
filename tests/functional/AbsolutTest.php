<?php
namespace Sereban\NeuronNet\Tests;

use Sereban\NeuronNet\Tests\Lib;
use Sereban\NeuronNet;
use Sereban\NeuronNet\Layout;
use Sereban\NeuronNet\Net;
use Sereban\NeuronNet\App;

class AbsoluteTest extends \PHPUnit_Framework_TestCase
{
    use Lib\Fixture;
    use Lib\Absolute;

    const WINNER_TEACHER_VALUE = 1;
    /** @var App  */
    protected $_app;

    /**
     * Set the main flags to app
     */
    public function setUp() {
        $this->_app = new App();
        Net::setDirection(Net::DIRECT);
        Net::setMode(Net::TEACHER_MODE);
        Net::setDebugFlag(false); //disable output to console
    }

    /**
     * Full Test: check whether back propagation was successful
     * @throws \Exception
     */
    public function testAbsolute() {
        $_values = $this->parseYaml($this->_getCurrentFolder(__DIR__, "absolute", "absolute.yml"));
        $_values = $this->prepareValues($_values);
        $this->initCounts($this->_app, $_values);
        //Initializing and start
        $this->_app->init($_values);
        $this->_app->start();
        Net::setMode(Net::PRODUCTION_MODE);
        $this->_app->start(); //run app with same values but in production mode

        $lastLayout = $this->_app->getLayoutCollection()->getLastLayout();
        /** @var NeuronNet\Neuron\React $winner */
        $winner     = $lastLayout->seek($this->_app->getWinner());
        //Compare standard teacher value with winner`s teacher value
        $this->assertEquals(self::WINNER_TEACHER_VALUE, $winner->getTeacherValue());
    }
}
