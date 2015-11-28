<?php
namespace Sereban\NeuronNet\Layout;

use \Sereban\NeuronNet\Net;
use \Sereban\NeuronNet\Layout;

class Collection implements \Iterator, \Countable
{
    /** @var  int */
    protected $_mode;
    /** @var  int */
    private $_level;
    /** @var  array -> the scope of all layouts */
    private $_layouts;
    /** @var array  */
    protected $_teacherValues = array();
    /** @var array  */
    protected $_initialValues = array();

    const INITIAL_VALUES = "initial_values";
    const TEACHER_VALUES = "teacher_values";
    /**
     * @return mixed
     */
    public function current() {
        return $this->_layouts[$this->_level];
    }

    /**
     * @param array $_values
     */
    public function initValues($_values) {
        if(isset($_values[self::INITIAL_VALUES]) && isset($_values[self::TEACHER_VALUES])) {
            $this->_teacherValues = $_values[self::TEACHER_VALUES];
            $this->_initialValues = $_values[self::INITIAL_VALUES];
        }
    }

    /**
     * @return Layout
     */
    public function getLastLayout() {
        return $this->seek($this->count() - 1);
    }

    /**
     * @param int $position
     * @return Layout
     */
    public function seek($position) {
        return $this->_layouts[$position];
    }

    public function next() {
        if($this->valid()) {
            if(Net::DIRECT == $this->_needToChangeDirection()) {
                Net::incrementIteration();
                if(Net::shouldReloadValues()) {
                    if(Net::isDebugEnabled()) {
                        var_dump("<h1>Reloading</h1>");
                    }
                    $this->reloadValues(); //reload values
                }
            } elseif(Net::REVERSE == $this->_needToChangeDirection()) {
                Net::setDirection(Net::REVERSE); //set reverse direction
            }

            $this->_level += Net::getLayoutCounter(); //go to next layout
        }
    }

    /**
     * @return int
     */
    protected function _needToChangeDirection() {
        $reverse = (int) ($this->_level + Net::getLayoutCounter() == $this->count()) * Net::REVERSE;
        $direct  = (int) ($this->_level == 1 && Net::isMistakeFlow()) * Net::DIRECT;

        return $reverse | $direct;
    }

    /**
     * @param bool|false $isReset
     * @return bool
     */
    public function reloadValues($isReset = false) {
        //Setting next teacher and initial values
        $_teacherValues = current($this->_teacherValues);
        $_initialValues = current($this->_initialValues);

        if((!$_teacherValues || !$_initialValues) && !$isReset) {
            reset($this->_initialValues);
            reset($this->_teacherValues);
            return $this->reloadValues(true); //to prevent ageless recursive loading
        }

        $this->_setInitialValues($_initialValues);
        $this->_setTeacherValues($_teacherValues);

        //Go to the next teacher values
        next($this->_teacherValues); //get to the next iteration
        next($this->_initialValues); //get to the next iteration

        return true;
    }

    /**
     * @param array $_values
     * @throws \Exception
     */
    protected function _setInitialValues(array $_values) {
        $this->seek(0)->setValues($_values);
    }

    /**
     * @param array $_values
     * @throws \Exception
     */
    protected function _setTeacherValues(array $_values) {
        $this->getLastLayout()->setValues($_values);
    }

    public function rewind() {
        //Start From the beginning
        $this->_level = 0;
        Net::setDirection(Net::DIRECT);
    }

    /**
     * @return int
     */
    public function key() {
        return $this->_level;
    }

    /**
     * @param Layout $layout
     */
    public function addLayout(Layout $layout) {
        $this->_layouts[] = $layout;
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->_layouts);
    }
    /**
     * Check what mode we have -> iterate only in one side when we are using production mode, and use back propagation if we have also mistake flow
     * @return bool
     */
    public function valid() {
        if(!Net::validateIteration())
            return false;

        if(!Net::isTeacherMode()) {
            return $this->_level != $this->count();
        } else { //back propaganation
            return !(($this->_level == $this->count() && !Net::isMistakeFlow()) &&
                ($this->_level == 0 && Net::isMistakeFlow()));
        }
    }

    /**
     * @param $_mode
     */
    public function setMode($_mode) {
        $this->_mode = $_mode;
    }

    /**
     * Remove All Layouts
     */
    public function flushLayouts() {
        $this->_layouts = array();
    }
}