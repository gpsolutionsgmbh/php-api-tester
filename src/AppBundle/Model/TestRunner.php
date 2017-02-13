<?php

namespace AppBundle\Model;

/**
 * Class TestRunner
 * @package AppBundle\Model
 */
class TestRunner
{
    protected $pool;
    protected $results;

    /**
     * TestRunner constructor.
     */
    public function __construct()
    {
        $this->results = new \Threaded();
        $this->pool = new \Pool(Config::getInstance()->getAppConfig()['threads_limit'], AutoloadWorker::class, [__DIR__ . '/../../../vendor/autoload.php', $this->results, new SafeLog()]);
    }

    /**
     * @return \Threaded
     */
    public function run()
    {
        //for ($i = 0; $i < 10; $i++) {
        $testCases = TestConfigAdapter::getTestCasesList();
        foreach ($testCases as $testCase) {
            $this->pool->submit(new TestCase($testCase));
        }
        //}
        $this->pool->shutdown();
        return $this->results;
    }

}