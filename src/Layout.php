<?php
namespace Sereban\NeuronNet;

use Sereban\NeuronNet\Neuron\React;

class Layout implements \IteratorAggregate
{
    /** @var  array */
    private $_neurons;
    /** @var  int */
    protected $_level;
    /** @var array  */
    protected $_signals = array();
    /** @var bool  */
    protected $_isLast = false;
    /** @var  float. Initialize value should be 1 */
    protected $_offsetWeight = 0;
    /** @var array. The min value in Neuron Net is 0 and max - 0.
     * Keys: [value] represent maximum value;
     * [index] -> The index of neuron which won
     */
    protected $_pretender = array(
        "value" => 0
    );

    /**
     * @param Neuron $_pretender
     */
    public function compare(Neuron $_pretender) {
        if($this->_isLast) { //only for React Neuron`s Layout
            if($_pretender->getValue() > $this->_pretender["value"]) {
                $this->_pretender["value"] = $_pretender->getValue();
                $this->_pretender["index"] = $_pretender->getIndex();
            }
        }
    }

    /**
     * Return the most popular neuron
     * @return array
     */
    public function getPretender() {
        return $this->_pretender;
    }
    /**
     * @param $isLast
     */
    public function setLast($isLast) {
        $this->_isLast = $isLast;
    }
    /**
     * Allows iterator interface in layout (** iterating neurons **)
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new \ArrayIterator($this->_neurons);
    }

    public function seek($position) {
        return $this->_neurons[$position];
    }

    /**
     * @return \Generator
     */
    public function neuronGenerator() {
        foreach($this->_neurons as $index => $neuron) {
            yield $index => $neuron;
        }
    }

    /**
     * @param Neuron $neuron
     */
    public function addNeuron(Neuron $neuron) {
        $this->_neurons[] = $neuron;
    }

    /**
     * @param array $values
     * @throws \Exception
     */
    public function setValues(array $values) {
        if(count($values) != count($this->_neurons))
            throw new \Exception("Cannot set values, because count values don`t match qty of neurons");
        //Reset arrays
        reset($values);
        reset($this->_neurons);

        foreach($this->neuronGenerator() as $neuron) {
            $_value = current($values);
            if($neuron instanceof React) {
                /** @var React $neuron */
                $neuron->setTeacherValue($_value);
            } else {
                /** @var Neuron\Stim $neuron */
                $neuron->setValue($_value, array("_force" => true)); //force update
            }

            next($values);
        }
    }

    /**
     * @param int $signal
     */
    public function addSignal($signal) {
        $this->_signals[] = $signal * $this->getOffset();
    }

    public function calculate() {
        $_sum = array_sum($this->_signals);
        $this->_signals = array();

        $this->setOffset($_sum);
    }

    /**
     * @return int
     */
    public function getLevel() {
        return $this->_level;
    }

    /**
     * @param $_level
     */
    public function setLevel($_level) {
        $this->_level = $_level;
    }
    /**
     * @return float
     */
    public function getOffset() {
        return $this->_offsetWeight;
    }

    /**
     * @param $_weight
     */
    public function setOffset($_weight) {
        $this->_offsetWeight = $_weight;
    }
}