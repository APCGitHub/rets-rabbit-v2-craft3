<?php


namespace apc\retsrabbit\transferobjects;

/**
 * Class ListingTransferObject
 *
 * @package apc\retsrabbit\transferobjects
 * @property bool $hasPhotos
 * @property integer $totalPhotos
 * @property array $data
 */
class ListingTransferObject extends TransferObject
{
    /**
     * Booleans
     * @var
     */
    protected $hasPhotos;

    /**
     * Integers
     * @var
     */
    protected $totalPhotos;

    /**
     * Arrays
     * @var
     */
    protected $data;

    /**
     * @param $property
     * @return mixed|null
     * @throws \Exception
     */
    public function __get($property)
    {
        if(property_exists($this, $property) === true ) {
            return parent::__get($property);
        }

        return $this->data[$property] ?? null;
    }

    /**
     * @param $property
     * @return bool
     */
    public function __isset($property)
    {
        if(property_exists($this, $property) === true ) {
            return true;
        }

        return array_key_exists($property, $this->data);
    }
}