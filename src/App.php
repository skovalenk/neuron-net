<?php
namespace Sereban\NeuronNet;


class App
{
    /** @var  Layout\Collection */
    protected $_layoutCollection;
    /** @var array  */
    protected $_neuronCount = array();

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getWinner() {
        $lastLayout = $this->_layoutCollection->getLastLayout();
        $pretender  = $lastLayout->getPretender();

        if(isset($pretender["index"]))
            return $pretender["index"];
        else
            throw new \Exception("There is no winner");
    }

    /**
     * @param array $_values -> key: initial_values = values of x neurons; teacher_values = values of z neurons
     */
    public function init(array $_values) {
        $this->_layoutCollection = new Layout\Collection();
        $level = 0; //level needed for
        $layout = null;

        foreach($this->_neuronCount as $type => $count) {
            if(is_array($count)) {
                foreach($count as $_c) {
                    $layout = $this->_initLayout($type, $_c, $level, $layout);
                    $this->_layoutCollection->addLayout($layout);
                }
            }
        }

        $layout->setLast(true); //mark this layout as last one
        //Add values to collection
        $this->_layoutCollection->initValues($_values);
        $this->_layoutCollection->reloadValues();
    }

    /**
     * key -> teacher value (0 or 1); value = array of value
     */
    public function start() {
        /**
         * @var int $level
         * @var Layout $layout
         */
        foreach($this->_layoutCollection as $level => $layout) {
            //$layout->calculate();
            /** @var Neuron $neuron */
            foreach($layout->neuronGenerator() as $neuron) {
                $neuron->calculate();
                $neuron->send();
            }
        }
    }

    /**
     * @param int $type
     * @param int $count
     * @param int $level
     * @param Layout $previousLayout
     * @return Layout
     */
    protected function _initLayout($type, $count, &$level, $previousLayout = null) {
        $layout   = new Layout();
        $layout->setLevel($level);

        for($i = 0; $i < $count; $i ++) {
            $_neuron = Factory::neuron($type, $i);
            $_neuron->setLayout($layout);
            $_neuron->hash();

            if(isset($previousLayout)) {
                /** @var Neuron $prevNeuron */
                foreach($previousLayout->neuronGenerator() as $prevNeuron) {
                    $_relation = new Relation($prevNeuron, $_neuron); //Init relation and add neurons there
                    //Set relation to neurons
                    $_neuron->addRelation($_relation, Relation::MISTAKE); //reverse
                    $prevNeuron->addRelation($_relation, Relation::SIGNAL);  //direct
                }
            }

            $layout->addNeuron($_neuron);
        }

        $level++;

        return $layout;
    }

    /**
     * @return Layout\Collection
     */
    public function getLayoutCollection() {
        return $this->_layoutCollection;
    }

    public function setNeuronCount($type, $count) {
        $this->_neuronCount[$type][] = $count;
    }
}