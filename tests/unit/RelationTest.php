<?php

namespace Sereban\NeuronNet\Tests;


use Sereban\NeuronNet\Factory;
use Sereban\NeuronNet\Tests\Lib;
use Sereban\NeuronNet\Relation;
use Sereban\NeuronNet\Neuron;
use Sereban\NeuronNet\Net;

class RelationTest extends \PHPUnit_Framework_TestCase
{
    use Lib\Fixture;
    use Lib\Relation;
    /** @var  Neuron */
    protected $_from;
    /** @var  Neuron */
    protected $_to;
    /** @var  Relation */
    protected $_relation;
    /** @var int Used in order to simplify process of calculating weights correction */
    protected $_oldValue = 1;

    public function setUp() {
        //Take Associated and Reaction Neurons -> in order to test direct and reverse directions
        $this->_from = Factory::neuron(Factory::ASSOC, 0);
        $this->_to   = Factory::neuron(Factory::REACT, 0);

        //Init relation
        $this->_relation = new Relation(
            $this->_from,
            $this->_to
        );

        Net::setMode(Net::TEACHER_MODE);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function prepareSendingData() {
        return $this->parseYaml($this->_getCurrentFolder(__DIR__, "relation", "send.yml"));
    }

    /**
     * Sending data from lower to higher neuron
     * @param $signal
     * @param $weight
     * @dataProvider prepareSendingData
     */
    public function testDirectSend($signal, $weight) {
        $this->_from->setValue($signal);
        $this->_relation->setWeight($weight);
        Net::setDirection(Net::DIRECT); //signal propagation

        $this->_relation->send($this->_from);

        $this->assertEquals(
            $signal * $weight,
            array_sum($this->_to->getSignals())
        );

        $this->_to->flushSignals(); //flush all signals
        $this->_relation->setWeight(Relation::DEFAULT_WEIGHT); //set default weight
    }

    /**
     * Sending data from higher to lower neuron
     * @param $signal
     * @param $weight
     * @dataProvider prepareSendingData
     */
    public function testReverseSend($signal, $weight) {
        $this->_to->setValue($signal);
        $this->_relation->setWeight($weight);
        Net::setDirection(Net::REVERSE); //signal propagation

        $this->_relation->send($this->_to);

        $this->assertEquals(
            $signal * $weight,
            array_sum($this->_from->getSignals())
        );

        $this->_from->flushSignals(); //flush all signals
        $this->_relation->setWeight(Relation::DEFAULT_WEIGHT); //set default weight
    }

    /**
     * Test in which limits initialized weight is
     * Should be from -0.5 to 0.5, but not in limit [-0.1, 0.1]
     */
    public function testWeightInitialization() {
        $initWeight = $this->initWeight();
        //Test weight limits
        $this->assertLessThanOrEqual(Relation::DEFAULT_WEIGHT, abs($initWeight));
        //Test weight on small values
        $this->assertGreaterThanOrEqual(Relation::SMALL_VALUE, abs($initWeight));
    }

    /**
     * Test Weight Correction after sending signal. Direction Should be always Reverse
     * @param $signal
     * @param $weight
     * @dataProvider prepareSendingData
     * @depends testDirectSend
     * @depends testReverseSend
     */
    public function testWeightCorrection($signal, $weight) {
        Net::setDirection(Net::REVERSE); //signal propagation
        $this->_to->setValue($signal);
        $this->_from->setValue($this->_oldValue);
        $this->_relation->setWeight($weight);

        $this->_relation->send($this->_to);
        //Test whether weight correction is null or no
        $this->assertNotEquals($weight, $this->getWeightFromRelation($this->_relation), "Back Propagation Stuck");
    }
}
