<?php

namespace App\Core\Forms;

use APIManager;
use App\Core\Forms\Form;

class SearchForm extends Form
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function validate()
    {
        foreach($this->data as $field)
        {
            if(is_null($field)) return false;
        }

        return true;
    }

    public function submit()
    {
        $searchData = APIManager::searchChannels($this->data['maxResults'], $this->data['search']);

        return [
            'searchData' => $searchData
        ];
    }
}