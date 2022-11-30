<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

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
