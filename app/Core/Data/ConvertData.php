<?php

namespace App\Core\Data;

class ConvertData
{
    public static function calculateAverage($array, $trackedZero, $type)
    {
        $sum = 0;
        $count = 0;
        $areViews = $type === 'views';

        foreach ($array as $value) {
            if(gettype($value) === 'string') {
                $value = json_decode($value);
                $arrayViews = $value->views;
                $arrayEarned = $value->earned;
            } else {
                $arrayViews = $value['views'];
                $arrayEarned = $value['earned'];
            }

            if($areViews && !is_null($value) && $trackedZero) {
                $number = $arrayViews - $trackedZero;
                $count++;
            } elseif(!$areViews && !is_null($value)) {
                $number = $arrayEarned;
                $count++;
            } else {
                $number = 0;
            }
        }

        $sum += $number;

        if($count === 0) {
            if($areViews) {
                return 0;
            } else {
                return number_format(0, 2);
            }
        }

        if($areViews) {
            return $sum / $count;
        } else {
            return number_format($sum / $count, 2);
        }
    }

    public static function exchangeCurrency($currency, $currencyExchange, $value)
    {
        switch($currency) {
            case 'HRK':
                $currentState = $currencyExchange->rates->HRK;
                return number_format($value * $currentState, 2);
                break;
            case 'EUR':
                $currentState = $currencyExchange->rates->EUR;
                return number_format($value * $currentState, 2);
                break;
            default:
                return number_format($value, 2);
                break;
        }
    }

    public static function addCurrency($value, $currency)
    {
        switch($currency) {
            case 'HRK':
                return $value . 'kn';
                break;
            case 'EUR':
                return $value . '€';
                break;
            default:
                return $value . '$';
                break;
        }
    }
}
