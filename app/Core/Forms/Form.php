<?php

namespace App\Core\Forms;

abstract class Form
{
    protected $data;
    protected $error;

    public function getError() { return $this->error; }
    public function setError($error) { $this->error = $error; }

    public function __construct($data)
    {
        $this->data = $data;
    }

    public abstract function validate();

    public abstract function submit();
}
