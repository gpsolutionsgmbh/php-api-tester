<?php

namespace AppBundle\Model;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class SafeLog
 * @package AppBundle\Model
 */
class SafeLog extends \Threaded
{

    protected $log;

    public function __construct()
    {
        $this->log = Container::getInstance()->getContainer()->get('logger');
    }

    /**
     * If logging were allowed to occur without synchronizing the output would be illegible
     * @param $result
     * @param int $threadId
     * @param string $failedMessage
     */
    public function log($result, $threadId = 0, $failedMessage = '')
    {
        if ($result[2]) {
            $message = ["<info>{$result[0]} - OK! $result[1] $threadId</info>", ""];
        } else {
            $message = ["<error>{$result[0]}/$result[3] - FAILED! $result[1] $threadId</error>"];
            if ($failedMessage) {
                $message[] = "<error>{$failedMessage}</error>";
            }
        }
        $this->synchronized(function ($message, $result, Logger $log, $threadId) {
            $output = Container::getInstance()->getContainer()->get('output');
            $output->writeln($message);
            $log->pushHandler(new StreamHandler(__DIR__ . '/../../../var/logs/test.log', Logger::INFO));
            if ($result[2]) {
                $log->info("{$result[0]} - $result[1]", [$threadId]);
            } else {
                $log->alert("{$result[0]}", [$threadId]);
            }
        }, $message, $result, $this->log, $threadId);
    }

}