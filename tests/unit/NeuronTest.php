<?php

namespace Sereban\NeuronNet\Tests;

use Sereban\NeuronNet\Neuron;
use Sereban\NeuronNet\Net;
use Sereban\NeuronNet\Tests\Lib;
use Sereban\NeuronNet\Factory;

class NeuronTest extends \PHPUnit_Framework_TestCase
{
    use Lib\Neuron;
    use Lib\Fixture;
    /** @var array  */
    protected $_neurons = array();
    /** @var array -> Signals sending to neurons */
    protected $_signals = array();

    protected function _prepareSignal() {
        try {
            if(empty($this->_signals)) {
                $this->_signals = $this->parseYaml($this->_getCurrentFolder(__DIR__, "neuron", "calculation.yml"));
            }
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function setUp() {
        Net::setMode(Net::TEACHER_MODE);
    }
    /**
     * @return array
     * @throws \Exception
     */
    public function prepareDirectSignals() {
        $this->_prepareSignal();

        return $this->_signals["direct"];
    }

    /**
     * @return array
     */
    public function prepareReverseSignals() {
        $this->_prepareSignal();

        return $this->_signals["reverse"];
    }

    /**
     * @param array $signals
     * @param null $result
     * @dataProvider prepareDirectSignals
     */
    public function testDirectCalculation($signals, $result) {
        /** @var Neuron $neuron */
        foreach($this->prepareNeuronToCalculation($signals, Net::DIRECT) as $type => $neuron) {
            $neuron->calculate(); //Calculation process
            $this->assertEquals($neuron->getValue(), $result[$type]);
        }
    }

    /**
     * @param array $signals
     * @param null $result
     * @dataProvider prepareReverseSignals
     */
    public function testReverseCalculation($signals, $result) {
        /** @var Neuron $neuron */
        foreach($this->prepareNeuronToReverseCalculation($signals) as $type => $neuron) {
            $neuron->calculate();
            $this->assertEquals($neuron->getValue(), $result[$type]);
        }
    }

    public function testCreation() {
        try {
            $types = $this->parseYaml($this->_getCurrentFolder(__DIR__, "neuron", "types.yml"));

            foreach($types as $type => $data) {
                if(isset($data["class"]) && isset($data["index"])) {
                    /** @var \Sereban\NeuronNet\Neuron $neuron */
                    $neuron = Factory::neuron($type, $data["index"]);
                    //Assertions
                    $this->assertInstanceOf($data["class"], $neuron);
                    $this->assertEquals($data["index"], $neuron->getIndex());
                }
            }
        }  catch(\Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
