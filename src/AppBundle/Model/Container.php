<?php

namespace AppBundle\Model;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class Container
 * @package AppBundle\Model
 */
class Container
{

    private static $instance;
    private $container;

    /**
     * @return Container
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
        $this->container = new ContainerBuilder();
        $this->container->register('logger', 'Monolog\Logger')
            ->addArgument('results');
        $this->container->register('output', 'Symfony\Component\Console\Output\ConsoleOutput');
    }

    private function __clone()
    {
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        return $this->container;
    }


}