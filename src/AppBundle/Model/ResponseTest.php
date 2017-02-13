<?php

namespace AppBundle\Model;

use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseTest
 * @package AppBundle\Model
 */
class ResponseTest
{

    protected $propertyIndex;
    protected $propertyValue;
    protected $methods;

    /**
     * ResponseTest constructor.
     * @param $propertyIndex
     * @param array $methods
     */
    public function __construct($propertyIndex, array $methods)
    {
        $this->propertyIndex = $propertyIndex;
        $this->methods = $methods;
    }

    /**
     * @param ResponseInterface $response
     * @uses exist, notExist, equal
     * @return bool
     */
    public function check(ResponseInterface $response)
    {
        if ($response->getStatusCode() != 200 || (count($this->methods) && !$response->getBody())) {
            return false;
        }
        $responseData = (string)$response->getBody();
        $responseData = json_decode($responseData, true);
        foreach ($this->methods as $method => $args) {
            $this->propertyValue = $this->getArrayValue($responseData, $this->propertyIndex);
            if (!call_user_func_array("self::$method", $args)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function getPropertyValue()
    {
        return $this->propertyValue;
    }

    /**
     * @param array $array
     * @param array $indexes
     * @return bool|mixed
     */
    protected function getArrayValue(array $array, array $indexes)
    {
        if (count($array) == 0 || count($indexes) == 0) {
            return false;
        }
        $index = array_shift($indexes);
        if (!array_key_exists($index, $array)) {
            return false;
        }
        $value = $array[$index];
        if (count($indexes) == 0) {
            return $value;
        }
        if (!is_array($value)) {
            return false;
        }
        return $this->getArrayValue($value, $indexes);
    }

    private function exist()
    {
        return !is_null($this->propertyValue);
    }

    private function notExist()
    {
        return is_null($this->propertyValue);
    }

    private function equal($val)
    {
        return $val === $this->propertyValue;
    }

}