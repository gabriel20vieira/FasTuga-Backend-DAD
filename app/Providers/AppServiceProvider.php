<?php

namespace App\Providers;

use App\Models\Types\PaymentType;
use Exception;
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
            $isRest10 = $value % 10 == 0;
            return $isRest10;
        });

        Validator::extend('user_points', function ($attribute, $value, $params, $validator) {
            return ((auth('api')->hasUser()
                && (auth('api')->user()->isManager() || auth('api')->user()->customer != null) ? auth('api')->user()->customer->canUsePoints($value) > 0 : true)
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
            if (!isset($params[0])) {
                throw new Exception("Reference field mustn't be empty. ( 'reference:related_field' )");
            }


            $data = $validator->getData();
            $ex = explode('.', $params[0]);
            foreach ($ex as $e) {
                $data = $data[$e];
            }

            switch ($data) {
                case PaymentType::MBWAY->value:
                    return is_numeric($value) && strlen($value) == 9 && $value[0] == 9;
                    break;
                case PaymentType::VISA->value:
                    return strlen($value) == 16 && $value[0] == 4;
                    break;
                case PaymentType::PAYPAL->value:
                    return filter_var($value, FILTER_VALIDATE_EMAIL);
                    break;
            }


            return false;
        });

        Validator::extend('phone', function ($attribute, $value, $params, $validator) {
            return preg_match("/^(\+?351)?(9|2)\d\d{7}$/", $value);
        });
    }
}
