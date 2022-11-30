<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Http\FormRequest;

trait StoresImages
{
    private function storeImage(FormRequest $request, $folder, $string)
    {
        if (!preg_match("/^\/.+/m", $folder)) {
            $folder = "/" . $folder;
        }
        $folder = "public$folder";

        if ($request->hasFile($string)) {
            $stored = Storage::putFile(
                $folder,
                $request->file($string)
            );
            return preg_replace("/^public/i", "/storage", $stored);
        } else if (preg_match("/^data:image\/\w.*;base64/i", $request->input($string))) {

            preg_match("/data:image\/(.*?);/", $request->input($string), $extension);
            $image = preg_replace('/data:image\/(.*?);base64,/', '', $request->input($string));
            $folder = $folder . "/" . Str::random() . "." . end($extension);

            $stored = Storage::put(
                $folder,
                base64_decode($image)
            );

            if ($stored) {
                return preg_replace("/^public/i", "/storage", $folder);
            }
        }

        abort(422, "Bad image provided.");
    }
}
