<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Core\Data\PageData;
use App\Http\Controllers\FormController;

class ChannelController extends Controller
{
    public function index()
    {
        $submitted = session()->get('submitted');

        $searchData = null;

        if(!is_null($submitted)) $searchData = $submitted['searchData'];

        $pageData = PageData::get();

        return view('index')->with(['searchData' => $searchData, 'data' => $pageData]);
    }

    public function executeForm(Request $request)
    {
        $formName = $request->formName;
        $formData = array_diff_key($request->all(), ['formName' => 'string', '_token' => 'string']);

        $controller = new FormController($formName, $formData);
        $form = $controller->instantiate();

        if($form->validate())
        {
            $submitted = $form->submit();

            if($submitted)
            {
                return redirect()->route('index')->with(['submitted' => $submitted]);
            }
            else
            {
                $error = $form->getError();
                session()->flash('error', $error);
            }
        }

        return redirect()->route('index');
    }
}
