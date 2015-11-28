<?php
namespace Sereban\NeuronNet\Neuron;

use Sereban\NeuronNet\Neuron; //abstract neuron
use  Sereban\NeuronNet\Net;

/**
 * Class React
 * @package Sereban\NeuronNet\Neuron
 */

class React extends Neuron
{
    /** @var  float */
    private $_teacherValue;

    /**
     * @param $_teacherValue
     */
    public function setTeacherValue($_teacherValue) {
        $this->_teacherValue = $_teacherValue;
    }

    /**
     * @return float
     */
    public function getTeacherValue() {
        return $this->_teacherValue;
    }

    /**
     * @throws \Exception
     * @return bool
     */
    public function calculate() {
        //Calculating value
        $_sum  = array_sum($this->getSignals());
        $_sum += $this->getLayout()->getOffset();
        $this->_value = $this->_binarSigmoid($_sum);

        if(Net::isTeacherMode()) {
            Net::setDirection(Net::REVERSE);
            $_value       = ( $this->_teacherValue - $this->_value ) * $this->_diffBinarSigmoid($_sum); //new value will be mistake
            if(Net::isDebugEnabled()) { //Print value and teacher value/
              var_dump("Summ: $_value. Teacher : " . $this->_teacherValue . ". Value: " . $this->_value);
            }
            $this->setValue($_value);
        } else {
            $this->getLayout()->compare($this); //add to lead neurons if this neuron has the max value
        }

        return true;
    }
}