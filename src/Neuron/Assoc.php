<?php
namespace Sereban\NeuronNet\Neuron;

use Sereban\NeuronNet\Neuron; //abstract neuron
use  Sereban\NeuronNet\Net;

/**
 * Class Assoc
 * @package Sereban\NeuronNet\Neuron
 */
class Assoc extends Neuron
{
    /**
     * @throws \Exception
     */
    public function calculate() {
        $_sum = array_sum($this->getSignals());

        switch(Net::getDirection()) {
            case Net::DIRECT:
                $this->setValue($this->_binarSigmoid($_sum) + $this->getLayout()->getOffset());
                break;
            case Net::REVERSE:
                $this->setValue($_sum * $this->_diffBinarSigmoid($this->_value));
        }
    }
}