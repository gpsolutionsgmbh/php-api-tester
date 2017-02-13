<?php

namespace AppBundle\Model;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class TestCase extends \Threaded
{

    private $settings;
    private $status;
    private $stackParams;
    private $failedMessage;

    /**
     * Worker object in which this Threaded is being executed
     * @var AutoloadWorker
     */
    protected $worker;

    public function __construct($name)
    {
        $this->settings = new TestConfigAdapter($name);
        $this->stackParams = $this->settings->getOptions();
    }

    public function run()
    {
        $client = new Client([
            'base_uri' => $this->settings->getApiBaseUri()
        ]);
        $testCaseDone = true;
        $apiCallTime = 0;
        $testName = '';
        foreach ($this->settings->getTests() as $testName) {
            $startTime = microtime(true);
            try {
                $uri = $this->setupStackParams($this->settings->getTestRequestUri($testName));
                $options = $this->setupStackParams($this->settings->getTestRequestOptions($testName));
                $response = $client->request(
                    $this->settings->getTestRequestMethod($testName),
                    $uri,
                    $options
                );
            } catch (RequestException $e) {
                $apiCallTime = microtime(true) - $startTime;
                $testCaseDone = false;
                $this->failedMessage = $e->getMessage();
                $this->alertFail($testName);
                //echo $this->settings->getTestCaseName() . '/' . $testName . ' - ' . 0 . ' ' . $this->worker->getThreadId() . PHP_EOL;
                break;
            }
            $apiCallTime = microtime(true) - $startTime;
            foreach ($this->settings->getTestResponseTests($testName) as $responseTestName) {
                $responseTest = new ResponseTest($this->settings->getResponseTestParameterIndex($responseTestName), $this->settings->getResponseTestParameterCheckMethods($responseTestName));
                $checkResult = $responseTest->check($response);
                //echo $this->settings->getTestCaseName() . '/' . $testName . ' - ' . 0 . ' ' . $this->worker->getThreadId() . PHP_EOL;
                if (!$checkResult) {
                    $testCaseDone = false;
                    $this->alertFail($testName);
                    break 2;
                } else {
                    $this->stackParams[$this->settings->getResponseTestParameterName($responseTestName)] = $responseTest->getPropertyValue();
                }
            }
        }
        if ($testCaseDone) {
            $this->alertRestored();
        }
        $result = [$this->settings->getTestCaseName(), $apiCallTime, $testCaseDone, $testName];
        if (extension_loaded("pthreads")) {
            $this->worker->addResult($result);
            $this->worker->getLogger()->log($result, $this->worker->getThreadId(), $this->failedMessage);
            exit;
        } else {
            $logger = new SafeLog();
            $logger->log($result);
        }
    }

    protected function setupStackParams($data)
    {
        if (is_array($data)) {
            $isJson = true;
            $str = json_encode($data);
        } else {
            $isJson = false;
            $str = $data;
        }
        foreach ($this->stackParams as $key => $value) {
            $str = str_replace('<' . strtoupper($key) . '>', $value, $str);
        }
        if ($isJson) {
            return json_decode($str, true);
        } else {
            return $str;
        }

    }

    protected function alertFail($testName)
    {
        $cache = new FilesystemAdapter();
        $alert = $cache->getItem($this->getAlertKey());
        if (!$alert->isHit()) {
            $this->sendAlert(
                "{$this->settings->getApiName()} Fail",
                "{$this->settings->getTestCaseName()}/$testName - was filed!"
            );
            $alert->set(true);
            $cache->save($alert);
        }
    }

    protected function alertRestored()
    {
        $cache = new FilesystemAdapter();
        $alert = $cache->getItem($this->getAlertKey());
        if ($alert->isHit()) {
            $this->sendAlert(
                "{$this->settings->getApiName()} Restored",
                "{$this->settings->getTestCaseName()} - was restored!"
            );
            $cache->deleteItem($this->getAlertKey());
        }
    }

    protected function getAlertKey()
    {
        return (string)md5($this->settings->getEmail() . $this->settings->getTestCaseName());
    }

    public function getStatus()
    {
        return $this->status;
    }

    protected function sendAlert($subject, $body)
    {
        /**
         * @var $message \Swift_Message
         */
        try {
            $config = Config::getInstance()->getAppConfig();
            $transport = \Swift_SmtpTransport::newInstance($config['mailer']['host'], $config['mailer']['port'], $config['mailer']['security']);
            if ($config['mailer']['username']) {
                $transport->setUsername($config['mailer']['username']);
            }
            if ($config['mailer']['password']) {
                $transport->setPassword($config['mailer']['password']);
            }
            $mailer = \Swift_Mailer::newInstance($transport);
            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom([$config['mailer']['from'] => 'API Tester'])
                ->setTo($this->settings->getEmail())
                ->setBody($body);
            return $mailer->send($message);
        } catch (\Swift_TransportException $e) {
            return 0;
        }
    }

}