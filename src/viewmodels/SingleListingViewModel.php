<?php


namespace apc\retsrabbit\viewmodels;


use Apc\RetsRabbit\Core\TransferObjects\Listing;
use apc\retsrabbit\decorators\ListingResourceDecorator;
use apc\retsrabbit\decorators\ResourceDecorator;

class SingleListingViewModel extends ViewModel
{
    /**
     * @var Listing
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
        return new ListingResourceDecorator();
    }

    /**
     * @param $data
     */
    public function decorateResource($data)
    {
        $this->data = $this->getDecorator()->setResource($data);
    }
}