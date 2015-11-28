<?php
namespace Sereban\NeuronNet\Tests\Lib;

use Sereban\NeuronNet\Factory;
use Sereban\NeuronNet;

trait Relation {
    protected $_initWeightMethod     = "_initWeight";
    /**
     * @param NeuronNet\Relation $relation
     * @return mixed
     */
    public function getWeightFromRelation(NeuronNet\Relation $relation) {
        $reflectionRelation = new \ReflectionClass($relation);
        $property =  $reflectionRelation
            ->getProperty("_weight");

        $property->setAccessible(true);

        return $property->getValue($relation);
    }

    /**
     * @param \Sereban\NeuronNet\Tests\Lib\Relation|null $relation
     * @return mixed
     */
    public function initWeight(Relation $relation = null) {
        if(empty($relation)) {
            $relationClass = new \ReflectionClass("Sereban\NeuronNet\Relation");

            $relation      = $relationClass->newInstance(
                Factory::neuron(Factory::ASSOC, 0),
                Factory::neuron(Factory::REACT, 0)
            );
        } else {
            $relationClass = new \ReflectionClass($relation);
        }
        //Init ReflectionMethod
        $initWeightMethod = $relationClass->getMethod($this->_initWeightMethod);
        $initWeightMethod->setAccessible(true);

        return $initWeightMethod->invoke($relation);
    }
}