<?php

namespace App\Core\Forms;

abstract class Form
{
    protected $data;
    protected $message;

    public function getMessage() { return $this->message; }
    public function setMessage($message) { $this->message = $message; }

    public function __construct($data)
    {
        $this->data = $data;
    }

    public abstract function validate();

    public abstract function submit();
}
