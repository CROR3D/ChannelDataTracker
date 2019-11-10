<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Core\Forms\SearchForm;

class FormController extends Controller
{
    private $class;
    private $data;

    public function __construct($name, $data)
    {
        $this->class = "\App\Core\Forms\\" . $name;
        $this->data = $data;
    }

    public function instantiate()
    {
        return new $this->class($this->data);
    }
}
