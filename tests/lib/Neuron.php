<?php
namespace Sereban\NeuronNet\Tests\Lib;

use Sereban\NeuronNet\Factory;
use Sereban\NeuronNet\Net;
use Sereban\NeuronNet;

trait Neuron
{
    /**
     * @param \Sereban\NeuronNet\Neuron $neuron
     */
    protected function _addLayoutToNeuron(NeuronNet\Neuron $neuron) {
        $layout = new NeuronNet\Layout();
        $neuron->setLayout($layout);
    }

    /**
     * @param array $signals
     * @param int $direction
     * @return array
     */
    public function prepareNeuronToCalculation($signals = array(), $direction) {
        $assoc   = Factory::neuron(Factory::ASSOC, 0);
        $react   = Factory::neuron(Factory::REACT, 0);
        //Adding Signals
        foreach($signals as $signal) {
            $assoc->addSignal($signal);
            $react->addSignal($signal);
        }
        $this->_addLayoutToNeuron($assoc);
        $this->_addLayoutToNeuron($react);
        //Calculation
        Net::setDirection($direction);
        return array(Factory::ASSOC => $assoc, Factory::REACT => $react);
    }

    /**
     * @param $signals
     * @return array
     */
    public function prepareNeuronToReverseCalculation($signals = array()) {
        $neurons = $this->prepareNeuronToCalculation($signals, Net::REVERSE);
        /** @var NeuronNet\Neuron\React $react */
        $react = $neurons[Factory::REACT];
        $react->setTeacherValue(1);
        return $neurons;
    }
}