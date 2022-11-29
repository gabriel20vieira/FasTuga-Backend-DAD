<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Http\FormRequest;

trait LoadsImages
{

    private function loadImage($image)
    {
        $file = null;
        foreach (config('image.search_path', []) as $p) {
            $path = preg_match("/\/$/", $p) ? $p : $p . '/';
            $path .= $image;
            if (File::exists($path)) {
                $file = $path;
            }
        }

        if (!$file) {
            abort(404, 'Not found');
        }

        return redirect()->to($file);
    }
}
