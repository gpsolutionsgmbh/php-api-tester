<?php

namespace AppBundle\Model;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Config
 * @package AppBundle\Model
 */
class Config
{

    const CONFIG_PATH = __DIR__ . '/../../../app/config/';

    private $testFlowConfig;
    private $appConfig;

    private static $instance;

    /**
     * @return Config
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->appConfig = Yaml::parse(
            file_get_contents(self::CONFIG_PATH . 'config.yml')
        );
        if (isset($this->appConfig['tests_config'])) {
            $testsConfig = $this->appConfig['tests_config'];
        } else {
            $testsConfig = 'api-tester.json';
        }
        $this->testFlowConfig = $this->json_clean_decode(
            file_get_contents(self::CONFIG_PATH . $testsConfig),
            true
        );
    }

    /**
     * Clean comments of json content and decode it with json_decode().
     * Work like the original php json_decode() function with the same params
     *
     * @param   string $json The json string being decoded
     * @param   bool $assoc When TRUE, returned objects will be converted into associative arrays.
     * @param   integer $depth User specified recursion depth. (>=5.3)
     * @param   integer $options Bitmask of JSON decode options. (>=5.4)
     * @return  array/object
     */
    private function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0)
    {
        // search and remove comments like /* */ and //
        $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            return json_decode($json, $assoc, $depth, $options);
        } elseif (version_compare(phpversion(), '5.3.0', '>=')) {
            return json_decode($json, $assoc, $depth);
        } else {
            return json_decode($json, $assoc);
        }
    }

    private function __clone()
    {
    }

    /**
     * @return array
     */
    public function getTestFlowConfig()
    {
        return $this->testFlowConfig;
    }

    /**
     * @return array
     */
    public function getAppConfig()
    {
        return $this->appConfig;
    }


}