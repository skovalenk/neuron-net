<?php
namespace Sereban\NeuronNet;

abstract class Neuron
{
    /** Default values */
    const DEFAULT_VALUE  = 0;
    const DEFAULT_WEIGHT = 0.5;
    /** @var  float */
    protected $_value;
    /** @var  string */
    private $_hash;
    /** @var   */
    private $_index;
    /** @var  Layout */
    private $_layout;
    /** @var  array -> SIGNAL AND MISTAKE */
    protected $_relations;
    /** @var  array -> entries of previous layout of neurons */
    protected $_signalValues = array();

    public function __construct($index) {
        $this->__initValue();
        $this->_index = $index;
    }

    /**
     * Make neuron unique
     */
    public function hash() {
        $this->_hash = base64_encode(serialize(array($this->getLayout()->getLevel(), $this->_index)));
    }

    /**
     * @return mixed
     */
    public function getIndex() {
        return $this->_index;
    }

    /**
     * @return string
     */
    public function getHash() {
        return $this->_hash;
    }

    /**
     * @return float
     */
    public function getValue() {
        return $this->_value;
    }

    /**
     * @return mixed
     */
    abstract public function calculate();

    /**
     * @param Layout $layout
     */
    public function setLayout(Layout $layout) {
        $this->_layout = $layout;
    }

    /**
     * @param $x
     * @return float
     */
    protected function _diffBinarSigmoid($x) {
        $binarSigmoid = $this->_binarSigmoid($x);

        return $binarSigmoid * (1 - $binarSigmoid);
    }
    /**
     * @param float $x
     * @return float
     */
    protected function _binarSigmoid($x) {
        return 1 / ( 1 + exp(-$x));
    }

    private function __initValue() {
        $this->_value  = self::DEFAULT_VALUE;
    }

    public function flushSignals() {
        $this->_signalValues = array();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function send() {
        //Choose @var int $_directionType
        $_directionType = (Net::getDirection() == Net::DIRECT) ? Relation::SIGNAL : Relation::MISTAKE;

        if(!isset($this->_relations[$_directionType]) || !is_array($this->_relations[$_directionType])) {
            if(Net::isTeacherMode()) {
                throw new \Exception("Cannot find direction: $_directionType");
            } else {
                return false; //Production Mode shouldn`t have reverse direction
            }
        }

        /** @var Relation $relation */
        foreach($this->_relations[$_directionType] as $relation) {
            $relation->send($this); //Sending from current neuron
        }

        $this->flushSignals();
        return true;
    }

    /**
     * @return Layout
     */
    public function getLayout() {
        return $this->_layout;
    }

    /**
     * @param Relation $relation
     * @param int $_type
     */
    public function addRelation(Relation $relation, $_type = Relation::SIGNAL) {
        $this->_relations[$_type][] = $relation;
    }

    /**
     * @return array
     */
    public function getSignals() {
        return $this->_signalValues;
    }

    /**
     * @param float $signalValue
     */
    public function addSignal($signalValue) {
        $this->_signalValues[] = $signalValue;
    }

    /**
     * Set value after it calculation
     * @param $_value
     * @throws \Exception
     */
    public function setValue($_value) {
        if(!is_numeric($_value))
            throw new \Exception("Value should be the number");

        $this->_value = $_value;
    }

    /**
     * Set x to init value
     */
    public function flushValue() {
        $this->__initValue();
    }
}