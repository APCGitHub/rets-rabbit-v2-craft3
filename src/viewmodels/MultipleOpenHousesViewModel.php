<?php


namespace apc\retsrabbit\viewmodels;


use Apc\RetsRabbit\Core\TransferObjects\OpenHouse;
use apc\retsrabbit\decorators\OpenHouseResourceDecorator;
use apc\retsrabbit\decorators\ResourceDecorator;

class MultipleOpenHousesViewModel extends ViewModel
{
    /**
     * @var OpenHouse[]
     */
    public $data;

    /**
     * @return bool
     */
    public function hasData(): bool
    {
        return $this->data !== null && count($this->data) > 0;
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
        foreach($data as $open_house) {
            $this->data[] = $this->getDecorator()->setResource($open_house);
        }
    }
}