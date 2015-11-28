<?php
namespace Sereban\NeuronNet\Tests\Lib;

use Sereban\NeuronNet\Layout;
use Sereban\NeuronNet\App;
use Sereban\NeuronNet;

trait Absolute
{
    /**
     * @param array $_values
     * @return array
     */
    public function prepareValues(array $_values) {
        $_initialValues = array();
        $_teacherValues = array();

        foreach($_values as $value) {
            $_initialValues[] = $value["initial_values"];
            $_teacherValues[] = $value["teacher_values"];
        }

        return array(
            Layout\Collection::INITIAL_VALUES => $_initialValues,
            Layout\Collection::TEACHER_VALUES => $_teacherValues
        );
    }

    /**
     * @param \Sereban\NeuronNet\App $app
     * @param array $_values
     */
    public function initCounts(App $app, array $_values) {
        $initCount    = count($_values[Layout\Collection::INITIAL_VALUES][0]);
        $teacherCount = count($_values[Layout\Collection::TEACHER_VALUES][0]);
        $assocCount   = (int)($initCount * NeuronNet\Relation::ACCELERATOR); //The number of associated neurons should be smaller than the number of stimulated
        //Setting counts
        $app->setNeuronCount(NeuronNet\Factory::STIM, $initCount);
        $app->setNeuronCount(NeuronNet\Factory::ASSOC, $assocCount);
        $app->setNeuronCount(NeuronNet\Factory::REACT, $teacherCount);
    }
}