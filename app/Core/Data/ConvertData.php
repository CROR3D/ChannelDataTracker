<?php

namespace App\Core\Data;

class ConvertData
{
    public static function calculateAverage($array, $type)
    {
        $sum = 0;
        $count = 0;
        $areViews = ($type === 'views');

        foreach ($array as $value) {

            if(is_null($value)) continue;

            if(gettype($value) === 'string')
            {
                $value = (array) json_decode($value);
            }

            if($areViews)
            {
                $number = $value['views'];
                $count++;
            }
            elseif(!$areViews)
            {
                $number = $value['earned'];
                $count++;
            }
            else
            {
                $number = 0;
            }

            $sum += $number;
        }

        if($count === 0)
        {
            if($areViews)
            {
                return 0;
            }
            else
            {
                return number_format(0, 2);
            }
        }

        if($areViews)
        {
            return $sum / $count;
        }
        else
        {
            return number_format($sum / $count, 2);
        }
    }

    public static function exchangeCurrency($currency, $currencyExchange, $value)
    {
        switch($currency) {
            case 'HRK':
                $currentState = $currencyExchange->rates->HRK;
                return $value * $currentState;
                break;
            case 'EUR':
                $currentState = $currencyExchange->rates->EUR;
                return $value * $currentState;
                break;
            default:
                return $value;
                break;
        }
    }

    public static function addCurrency($value, $currency)
    {
        switch($currency) {
            case 'HRK':
                return number_format($value, 2) . 'kn';
                break;
            case 'EUR':
                return number_format($value, 2) . '€';
                break;
            default:
                return number_format($value, 2) . '$';
                break;
        }
    }
}
