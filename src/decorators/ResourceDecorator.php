<?php


namespace apc\retsrabbit\decorators;


use Apc\RetsRabbit\Core\TransferObjects\RetsRabbitTransferObject;

interface ResourceDecorator
{
    /**
     * @param RetsRabbitTransferObject $resource
     */
    public function setResource(RetsRabbitTransferObject $resource);
}