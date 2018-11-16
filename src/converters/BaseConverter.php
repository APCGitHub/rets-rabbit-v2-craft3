<?php


namespace apc\retsrabbit\converters;


use apc\retsrabbit\transferobjects\TransferObject;

abstract class BaseConverter
{
    /**
     * @param array|null $data
     * @return TransferObject
     */
    abstract public function parse($data = []): TransferObject;

    /**
     * @param array $data
     * @param BaseConverter $converter
     * @return array
     */
    public function parseCollection(array $data = [], BaseConverter $converter): array
    {
        $values = [];

        foreach($data as $d) {
            $values[] = $converter->parse($d);
        }

        return $values;
    }
}