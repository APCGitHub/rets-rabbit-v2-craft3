<?php


namespace apc\retsrabbit\converters;


use apc\retsrabbit\transferobjects\ListingTransferObject;
use apc\retsrabbit\transferobjects\TransferObject;

class ListingConverter extends BaseConverter
{
    /**
     * @param array|null $data
     * @return TransferObject|ListingTransferObject
     */
    public function parse($data = []): TransferObject
    {
        $listing              = new ListingTransferObject();
        $count                = isset($data['listing']['photos']) ? count($data['listing']['photos']) : 0;
        $listing->totalPhotos = $count;
        $listing->hasPhotos   = $count > 0;
        $listing->data        = $data;

        return $listing;
    }
}