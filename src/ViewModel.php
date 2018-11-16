<?php


namespace apc\retsrabbit;


class ViewModel
{
    /**
     * @var array
     */
    public $data;

    /**
     * @var array
     */
    public $errors;

    /**
     * @return bool
     */
    public function hasData(): bool
    {
        return $this->data !== null && count($this->data) > 0;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return $this->errors !== null && !empty($this->errors);
    }
}