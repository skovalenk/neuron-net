<?php
namespace Sereban\NeuronNet\Neuron;

use Sereban\NeuronNet\Neuron; //abstract neuron
use  Sereban\NeuronNet\Net;

/**
 * Class Stim
 * @package Sereban\NeuronNet\Neuron
 */

class Stim extends Neuron
{
    /**
     * @throws \Exception
     */
    public function calculate() {
        Net::setDirection(Net::DIRECT);
        $this->setValue($this->_value); //do nothing
    }

    /**
     * @param $value
     * @param $options
     */
    public function setValue($value, $options = array()) {
        //We should set initial values only one time
        if(empty($this->_value) || isset($options["_force"])) {
            $this->_value = $value;
        }
    }
}