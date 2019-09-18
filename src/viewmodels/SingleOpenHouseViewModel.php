<?php


namespace apc\retsrabbit\viewmodels;


use Apc\RetsRabbit\Core\TransferObjects\OpenHouse;
use apc\retsrabbit\decorators\OpenHouseResourceDecorator;
use apc\retsrabbit\decorators\ResourceDecorator;

class SingleOpenHouseViewModel extends ViewModel
{
    /**
     * @var OpenHouse
     */
    public $data;

    /**
     * @return bool
     */
    public function hasData(): bool
    {
        return $this->data !== null;
    }

    /**
     * @return ResourceDecorator
     */
    protected function getDecorator(): ResourceDecorator
    {
        return new OpenHouseResourceDecorator();
    }

    /**
     * @param $data
     */
    public function decorateResource($data)
    {
        $this->data = $this->getDecorator()->setResource($data);
    }
}