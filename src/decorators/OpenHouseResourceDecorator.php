<?php


namespace apc\retsrabbit\decorators;


use Apc\RetsRabbit\Core\TransferObjects\OpenHouse;
use Apc\RetsRabbit\Core\TransferObjects\RetsRabbitTransferObject;

class OpenHouseResourceDecorator implements ResourceDecorator
{
    /**
     * @var OpenHouse
     */
    protected $resource;

    /**
     * @param RetsRabbitTransferObject|OpenHouse $resource
     * @return OpenHouseResourceDecorator
     */
    public function setResource(RetsRabbitTransferObject $resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $name !== 'resource' && $name !== 'setResource';
    }

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if(!property_exists($this, $property)) {
            return $this->resource->{$property};
        }

        return null;
    }
}