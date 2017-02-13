<?php

namespace AppBundle\Model;

/**
 * Class TestConfigAdapter
 * @package AppBundle\Model
 */
class TestConfigAdapter
{

    protected $name;
    protected $config;
    protected $testCaseSettings;
    protected $tests;
    protected $options;

    /**
     * TestConfigAdapter constructor.
     * @param $testCaseName
     */
    public function __construct($testCaseName)
    {
        $this->config = Config::getInstance()->getTestFlowConfig();
        $this->testCaseSettings = self::getTestCasesSettings()[$testCaseName];
        $this->apiSettings = $this->config['api_settings'][$this->testCaseSettings['api']];
        $this->name = $testCaseName;
        $this->tests = $this->testCaseSettings['tests'];
        $this->options = isset($this->apiSettings['options']) ? $this->apiSettings['options'] : [];
    }

    /**
     * @return array
     */
    public static function getTestCasesList()
    {
        return array_keys(self::getTestCasesSettings());
    }

    /**
     * @return mixed
     */
    protected static function getTestCasesSettings()
    {
        $config = Config::getInstance()->getTestFlowConfig();
        return $config['test_cases'];
    }

    /**
     * @return mixed
     */
    public function getTests()
    {
        return $this->tests;
    }

    /**
     * @return mixed
     */
    public function getApiName()
    {
        return $this->testCaseSettings['api'];
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return mixed
     */
    public function getTestCaseName()
    {
        return $this->name;
    }

    /**
     * @param $testName
     * @return mixed
     */
    public function getTestRequestUri($testName)
    {
        return $this->getTestSettings($testName)['uri'];
    }

    /**
     * @param $testName
     * @return mixed
     */
    public function getTestRequestMethod($testName)
    {
        return $this->getTestSettings($testName)['method'];
    }

    /**
     * @param $testName
     * @return array
     */
    public function getTestRequestOptions($testName)
    {
        return isset($this->getTestSettings($testName)['options']) ? $this->getTestSettings($testName)['options'] : [];
    }

    /**
     * @param $testName
     * @return mixed
     */
    protected function getTestSettings($testName)
    {
        return $this->config['tests'][$testName];
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return self::getTestCasesSettings()[$this->name]['email'];
    }

    /**
     * @return mixed
     */
    public function getApiBaseUri()
    {
        return $this->config['api_settings'][self::getTestCasesSettings()[$this->name]['api']]['base_uri'];
    }

    /**
     * @param $testName
     * @return mixed
     */
    public function getTestResponseTests($testName)
    {
        return $this->getTestSettings($testName)['response_tests'];
    }

    /**
     * @param $responseTestName
     * @return mixed
     */
    protected function getResponseTestSettings($responseTestName)
    {
        return $this->config['response_tests'][$responseTestName];
    }

    /**
     * @param $responseTestName
     * @return mixed
     */
    public function getResponseTestParameterIndex($responseTestName)
    {
        return $this->getResponseTestSettings($responseTestName)['index'];
    }

    /**
     * @param $responseTestName
     * @return mixed
     */
    public function getResponseTestParameterCheckMethods($responseTestName)
    {
        return $this->getResponseTestSettings($responseTestName)['check_methods'];
    }

    /**
     * @param $responseTestName
     * @return mixed
     */
    public function getResponseTestParameterName($responseTestName)
    {
        return $this->getResponseTestSettings($responseTestName)['name'];
    }

}