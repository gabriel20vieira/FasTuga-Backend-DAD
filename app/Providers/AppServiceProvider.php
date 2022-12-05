<?php

namespace App\Providers;

use App\Models\Types\PaymentType;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('imageable', function ($attribute, $value, $params, $validator) {
            if (preg_match("/^https?:\/\//m", $value)) {
                return true;
            }

            try {
                ImageManagerStatic::make($value);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        });

        Validator::extend('points', function ($attribute, $value, $params, $validator) {
            return $value % 10 == 0;
        });

        Validator::extend('user_points', function ($attribute, $value, $params, $validator) {
            return ((auth('api')->hasUser()
                && (auth('api')->user()->isManager() || auth('api')->user()->customer != null) ? auth('api')->user()->customer->points - $value > 0 : true)
            );
        });

        Validator::extend('nif', function ($attribute, $value, $params, $validator) {
            if (strlen($value) != 9) {
                return false;
            }

            $sum = 0;
            for ($i = 7; $i >= 0; $i--) {
                $sum += ($value[$i] * (9 - $i));
            }

            $rest = $sum % 11;
            if ($rest == 0 || $rest == 1) {
                return $value[8] == 0;
            }

            return (11 - $rest) == $value[8];
        });

        Validator::extend('reference', function ($attribute, $value, $params, $validator) {
            $data = $validator->getData();
            $ex = explode('.', $params[0]);
            foreach ($ex as $e) {
                $data = $data[$e];
            }

            switch ($data) {
                case PaymentType::MBWAY->value:
                    return is_numeric($value) && strlen($value) == 9;
                    break;
                case PaymentType::VISA->value:
                    return strlen($value) == 16;
                    break;
                case PaymentType::PAYPAL->value:
                    return filter_var($value, FILTER_VALIDATE_EMAIL);
                    break;
            }


            return false;
        });
    }
}
