<?php
namespace Sereban\NeuronNet\Tests\Lib;

use Sereban\NeuronNet\Factory;
use Sereban\NeuronNet\Neuron;
use Sereban\NeuronNet;

trait Layout {
    /**
     * @param $index
     * @return \Sereban\NeuronNet\Layout
     */
    public function initLayout($index) {
        $layout = new NeuronNet\Layout();
        $layout->setLevel($index);

        return $layout;
    }

    /**
     * @param $number
     * @return \Sereban\NeuronNet\Layout\Collection
     */
    public function initAllLayouts($number) {
        $collection = new NeuronNet\Layout\Collection();

        for($i = 0; $i < $number; $i ++ ) {
            $collection->addLayout($this->initLayout($i));
        }

        return $collection;
    }

    /**
     * @param \Sereban\NeuronNet\Layout\Collection $layoutCollection
     * @param array $initialValues
     * @param array $teacherValues
     * @throws \Exception
     */
    public function initNeuronsInLayoutCollection(NeuronNet\Layout\Collection &$layoutCollection, $initialValues, $teacherValues) {
        $layoutIndex   = 0;
        /** @var array $neuronsSchema -> $type: $count*/
        $neuronsSchema = array(
            Factory::STIM  => count($initialValues),
            Factory::ASSOC => count($initialValues),
            Factory::REACT => count($teacherValues)
        );

        foreach($neuronsSchema as $type => $count) {
            $layout = $layoutCollection->seek($layoutIndex++);

            for ($j= 0; $j < $count; $j ++) {
                /** @var Neuron $neuron */
                $neuron = Factory::neuron($type, $j);
                $layout->addNeuron($neuron);
                $neuron->setLayout($layout);
            }
            //Set Data to Neurons
            if($type == Factory::STIM) {
                $layout->setValues($initialValues);
            } elseif($type == Factory::REACT) {
                $layout->setValues($teacherValues);
            }
        }
    }
}