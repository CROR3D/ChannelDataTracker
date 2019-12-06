<?php

namespace App\Http\Controllers;

use stdClass;
use Illuminate\Http\Request;
use App\Core\Data\PageData;
use App\Http\Controllers\FormController;

class ChannelController extends Controller
{
    public function index()
    {
        $submitted = session()->get('submitted');
        $connectionStatus = 'ACTIVE';

        if($submitted)
        {
            $searchData = new stdClass();
            $searchData = $submitted['searchData'];
        }
        else
        {
            $searchData = null;
        }

        $pageData = PageData::get();

        if(!$pageData && !is_array($pageData)) $connectionStatus = 'LOST';

        return view('index')->with(['searchData' => $searchData, 'data' => $pageData, 'connectionStatus' => $connectionStatus]);
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
                if($message = $form->getMessage()) session()->flash('success', $message);

                return redirect()->route('index')->with(['submitted' => $submitted]);
            }
            else
            {
                $error = $form->getMessage();
                session()->flash('error', $error);
            }
        }

        return redirect()->route('index');
    }
}
