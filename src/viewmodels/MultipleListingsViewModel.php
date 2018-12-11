<?php


namespace apc\retsrabbit\viewmodels;


use Apc\RetsRabbit\Core\TransferObjects\Listing;
use apc\retsrabbit\decorators\ListingResourceDecorator;
use apc\retsrabbit\decorators\ResourceDecorator;

class MultipleListingsViewModel extends ViewModel
{
    /**
     * @var Listing[]
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
        return new ListingResourceDecorator();
    }

    /**
     * @param $data
     */
    public function decorateResource($data)
    {
        foreach($data as $listing) {
            $this->data[] = $this->getDecorator()->setResource($listing);
        }
    }
}