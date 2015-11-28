<?php
namespace Sereban\NeuronNet\Tests\Lib;

use Symfony\Component\Yaml\Yaml;

trait Fixture
{
    protected $_defaultFixtureFolder = "Fixture";
    /**
     * @return string
     */
    public function getFixtureFolder() {
       if(!defined("FIXTURE_FOLDER")) {
           $_fixtureFolder = $this->_defaultFixtureFolder;
       } else {
           $_fixtureFolder = FIXTURE_FOLDER;
       }

       return $_fixtureFolder;
    }

    /**
     * @param $filePath
     * @return array
     * @throws \Exception
     */
    public function parseYaml($filePath) {
        if(file_exists($filePath)) {
            return Yaml::parse($filePath);
        } else {
            throw new \Exception("Cannot find yml file. Saw here: $filePath");
        }
    }

    /**
     * @return string
     */
    protected function _getCurrentFolder($directory, $type, $file) {
        return $directory . DS . $this->getFixtureFolder() . DS . ucfirst($type) . DS . $file;
    }
}