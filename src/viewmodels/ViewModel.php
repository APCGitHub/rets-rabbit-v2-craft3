<?php


namespace apc\retsrabbit\viewmodels;


use Apc\RetsRabbit\Core\TransferObjects\Error;
use apc\retsrabbit\decorators\ResourceDecorator;

abstract class ViewModel
{
    /**
     * @var Error
     */
    public $error;

    /**
     * @return bool
     */
    abstract public function hasData(): bool;

    /**
     * @return ResourceDecorator
     */
    abstract protected function getDecorator(): ResourceDecorator;

    /**
     * @param $data
     */
    abstract public function decorateResource($data);

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return $this->error !== null;
    }
}