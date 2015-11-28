<?php
namespace Sereban\NeuronNet;

class Relation
{
    //Neurone keys
    const LOWER_NEURON   = 0;
    const HIGHER_NEURON  = 1;
    //Weight Constants
    const DEFAULT_WEIGHT = 0.5;
    const SMALL_VALUE    = 0.1;
    //Types of Relations
    const SIGNAL         = 2;
    const MISTAKE        = 8;
    const ACCELERATOR    = 0.5;

    /** @var array -> Everywhere will have 2 neurons. */
    protected $_neurons = array();
    /** @var float  */
    protected $_weight = self::DEFAULT_WEIGHT;

    /**
     * @param Neuron $_from
     * @param Neuron $_to
     */
    public function __construct(Neuron $_from, Neuron $_to) {
        //Add neurons to relation
        $this->_neurons[self::LOWER_NEURON]  = $_from;
        $this->_neurons[self::HIGHER_NEURON] = $_to;

        $this->_initWeight();
    }

    /**
     * @param Neuron $from
     * @throws \Exception
     */
    public function send(Neuron $from) {
        //Choose @var int $_directionType
        $_directionType = (Net::getDirection() == Net::DIRECT) ? self::HIGHER_NEURON : self::LOWER_NEURON;

        if(!isset($this->_neurons[$_directionType]))
            throw new \Exception("Something wrong is with direction ({$_directionType}) or relation wasn`t init.");
        /** @var Neuron $to */
        $to = $this->_neurons[$_directionType];
        //var_dump("Value: " . $from->getValue() . " Class: " . get_class($from));
        $to->addSignal($this->_weight * $from->getValue());
        // If we are on mistake step we need to correct weights
        if(Net::isMistakeFlow()) {
            $this->_weightCorrection($to, $from);
        }

    }

    /**
     * Init weight from -0.5 to 0.5, except small values in range: -0.1, 0.1
     * @return float
     */
    protected function _initWeight() {
        $_randomWeight = rand(-self::DEFAULT_WEIGHT * 1000, self::DEFAULT_WEIGHT * 1000) / 1000; //random range

        if($_randomWeight > self::SMALL_VALUE || $_randomWeight < - self::SMALL_VALUE) { // in range between 0.1 and -0.1 - avoiding small values
            $this->_weight = $_randomWeight;
        }

        return $this->_weight;
    }

    /**
     * @param $weight
     */
    public function setWeight($weight) {
        $this->_weight = $weight;
    }

    /**
     * @param \Sereban\NeuronNet\Neuron $to
     * @param \Sereban\NeuronNet\Neuron $from
     * @return bool
     */
    protected function _weightCorrection(Neuron $to, Neuron $from) {
        $weightDiff    = self::ACCELERATOR * $from->getValue() * $to->getValue();

        $this->_weight += $weightDiff; //increase weight
        return true;
    }
}