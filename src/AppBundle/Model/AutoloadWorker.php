<?php

namespace AppBundle\Model;

/**
 * Class AutoloadWorker
 * @package AppBundle\Model
 */
class AutoloadWorker extends \Worker
{

    protected $loader;
    protected $results;
    protected $logger;

    /**
     * AutoloadWorker constructor.
     * @param $loader
     * @param \Threaded $results
     * @param SafeLog $logger
     */
    public function __construct($loader, \Threaded $results, SafeLog $logger)
    {
        $this->loader = $loader;
        $this->results = $results;
        $this->logger = $logger;
    }

    /**
     * Include autoloader for Tasks
     */
    public function run()
    {
        require_once($this->loader);
    }

    /**
     * Override default inheritance behaviour for the new threaded context
     * @param int $options PTHREADS_INHERIT_ALL = 1118481
     * @return bool
     */
    public function start(int $options = 1118481)
    {
        /**
         * PTHREADS_INHERIT_NONE = 0
         */
        return parent::start(0);
    }

    /**
     * @return \Threaded
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param array $result
     */
    public function addResult(array $result)
    {
        $this->results[] = (array)$result;
    }

    /**
     * @return SafeLog
     */
    public function getLogger()
    {
        return $this->logger;
    }

}
