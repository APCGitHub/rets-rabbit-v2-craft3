<?php


namespace apc\retsrabbit\decorators;


use Apc\RetsRabbit\Core\TransferObjects\Listing;
use Apc\RetsRabbit\Core\TransferObjects\RetsRabbitTransferObject;

class ListingResourceDecorator implements ResourceDecorator
{
    /**
     * @var Listing
     */
    protected $resource;

    /**
     * @param RetsRabbitTransferObject|Listing $resource
     * @return ListingResourceDecorator
     */
    public function setResource(RetsRabbitTransferObject $resource): ListingResourceDecorator
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return int
     */
    public function hasPhotos(): int
    {
        return count($this->resource->listing->photos);
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $name !== 'resource' && $name !== 'hasPhotos' && $name !== 'setResource';
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