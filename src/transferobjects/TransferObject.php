<?php


namespace apc\retsrabbit\transferobjects;


use Exception;

abstract class TransferObject
{
    /**
     * TransferObject constructor.
     *
     * @param array|null $data
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $property => $value) {
            $this->{$property} = $value;
        }
    }

    /**
     * Set
     *
     * @param $property
     * @param $value
     * @throws Exception
     */
    public function __set($property, $value)
    {
        $this->throwUnlessPropertyExists($property);
        $this->throwUnlessCorrectType($property, $value);
        $this->throwUnlessCorrectSubclass($property, $value);
        $this->{$property} = $value;
    }

    /**
     * Get
     *
     * @param $property
     *
     * @return mixed
     * @throws Exception
     */
    public function __get($property)
    {
        $this->throwUnlessPropertyExists($property);
        return $this->{$property};
    }

    /**
     * Get Class Used To Be
     *
     * @param $property
     *
     * @return string
     */
    private function getClassUsedToBe($property): string
    {
        return get_class($this->{$property});
    }

    /**
     * Get Type Of
     *
     * @param $value
     *
     * @return string
     */
    private function getTypeOf($value): string
    {
        return strtolower(gettype($value));
    }

    /**
     * Get Type Used To Be
     *
     * @param $property
     *
     * @return string
     */
    private function getTypeUsedToBe($property): string
    {
        return $this->getTypeOf($this->{$property});
    }

    /**
     * Get Type Wants To Be
     *
     * @param $value
     *
     * @return string
     */
    private function getTypeWantsToBe($value): string
    {
        return $this->getTypeOf($value);
    }

    /**
     * Throw Unless Correct Subclass
     *
     * @param $property
     * @param $value
     * @throws Exception
     */
    private function throwUnlessCorrectSubclass($property, $value)
    {
        $typeUsedToBe = $this->getTypeUsedToBe($property);
        if ($typeUsedToBe === 'object') {
            $classUsedToBe = $this->getClassUsedToBe($property);
            if (!is_subclass_of($value, $classUsedToBe)) {
                throw new Exception();
            }
        }
    }

    /**
     * Throw Unless Correct Type
     *
     * @param $property
     * @param $value
     * @throws Exception
     */
    private function throwUnlessCorrectType($property, $value)
    {
        $typeUsedToBe = $this->getTypeUsedToBe($property);
        if ($typeUsedToBe !== 'null') {
            $typeWantsToBe = $this->getTypeWantsToBe($value);
            if ($typeUsedToBe !== $typeWantsToBe) {
                throw new Exception();
            }
        }
    }

    /**
     * Throw Unless Property Exists
     *
     * @param $property
     * @throws Exception
     */
    private function throwUnlessPropertyExists($property)
    {
        if (property_exists($this, $property) !== true) {
            throw new Exception();
        }
    }
}
