<?php

    $vars = Session::all();
    foreach ($vars as $key => $value) {
        switch($key) {
            case 'success':
            case 'info':
                ?>
                <h4 class="mb-5 lead text-success notification">{!! $value !!}</h4>
                <?php
                Session::forget($key);
                break;
            case 'error':
            case 'warning':
                ?>
                <h4 class="mb-5 lead text-danger notification">{!! $value !!}</h4>
                <?php
                Session::forget($key);
                break;
            default:
        }
    }

?>
