<?php

    $vars = Session::all();
    foreach ($vars as $key => $value) {
        switch($key) {
            case 'success':
            case 'error':
            case 'warning':
            case 'info':
                ?>
                <h4 class="mb-5 lead text-danger notification">{!! $value !!}</h4>
                <?php
                Session::forget($key);
                break;
            default:
        }
    }

?>
