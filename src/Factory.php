<?php
namespace Sereban\NeuronNet;

use Sereban\NeuronNet\Neuron\Stim;
use Sereban\NeuronNet\Neuron\Assoc;
use Sereban\NeuronNet\Neuron\React;

final class Factory
{
    private function __construct() {
        //Use class as singleton
    }

    const STIM_LEVEL = 0;
    /** Type on Neurons */
    const STIM  = "stimulated";
    const ASSOC = "associated";
    const REACT = "reaction";

    /**
     * @param $type
     * @param int $index
     * @param null $entryValue
     * @return Assoc|React|Stim|null
     * @throws \Exception
     */
    public static function neuron($type, $index, $entryValue = null) {
        $_neuron = null;

        switch($type) {
            case self::REACT:
                $_neuron = new React($index);

                if(!empty($entryValue)) {
                    $_neuron->setTeacherValue($entryValue);
                }
                break;
            case self::ASSOC:
                $_neuron = new Assoc($index);
                break;
            case self::STIM:
            default:
                $_neuron = new Stim($index);

                if(!empty($entryValue))
                    $_neuron->setValue($entryValue);
        }

        return $_neuron;
    }
}