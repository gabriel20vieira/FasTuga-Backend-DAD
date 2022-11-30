<?php

namespace App\Providers;

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
            try {
                ImageManagerStatic::make($value);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        });

        Validator::extend('nif', function ($attribute, $value, $params, $validator) {
            if (strlen($value) != 9) {
                return false;
            }

            $sum = 0;
            for ($i = 8; $i > 0; $i--) {
                $sum += ($value[$i - 1] * (10 - $i));
            }

            $rest = $sum % 11;
            if ($rest == 0 || $rest == 1) {
                return $value[8] == 0;
            }

            return (11 - $rest) == $value[8];
        });
    }
}
