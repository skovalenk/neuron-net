<?php
namespace Sereban\NeuronNet\Tests;

use Sereban\NeuronNet\Tests\Lib;
use Sereban\NeuronNet;
use Sereban\NeuronNet\Net;

class LayoutTest extends \PHPUnit_Framework_TestCase
{
    use Lib\Fixture;
    use Lib\Layout;

    const LAYOUT_NUMBER = 3;
    const ONE_ITERATION_BEFORE = -1;

    /** @var  NeuronNet\Layout\Collection */
    protected $_layoutCollection;
    protected $_iterationArray = array(0 ,1 ,2, 1);

    /**
     * Prepare layout Collection by adding layouts there
     */
    public function setUp() {
        Net::setMode(Net::TEACHER_MODE);
        Net::setAllowedIterationsCount(1);
        Net::setReloadValuesVia(2);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function prepareValues() {
        return $this->parseYaml($this->_getCurrentFolder(__DIR__, "neuron", "values.yml"));
    }

    /**
     * @param $initialValues
     * @param $teacherValues
     * @dataProvider prepareValues
     */
    public function testIterations($initialValues, $teacherValues) {
        $this->_layoutCollection = $this->initAllLayouts(self::LAYOUT_NUMBER);
        Net::setIterationsCount(0);
        //Preparing collection with neurons
        $this->initNeuronsInLayoutCollection($this->_layoutCollection, $initialValues, $teacherValues);
        $_iterations = array();

        foreach($this->_layoutCollection as $layout) {
            array_push($_iterations, $layout->getLevel());
        }

        $this->assertSame($this->_iterationArray, $_iterations);
    }
}
