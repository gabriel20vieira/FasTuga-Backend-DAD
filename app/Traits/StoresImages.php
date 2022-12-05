<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Http\FormRequest;

trait StoresImages
{
    private function storeImage(FormRequest $request, $folder, $string)
    {
        if ($request->{$string} == null || preg_match("/^https?:\/\//m", $request->{$string})) {
            return null;
        }

        if (!preg_match("/^\/.+/m", $folder)) {
            $folder = "/" . $folder;
        }
        $folder = "public$folder";
        $stored = null;

        if ($request->hasFile($string) && $request->file($string)->isValid()) {
            $stored = Storage::putFile(
                $folder,
                $request->file($string)
            );
        } else if (preg_match("/^data:image\/\w.*;base64/i", $request->input($string))) {

            preg_match("/data:image\/(.*?);/", $request->input($string), $extension);
            $image = preg_replace('/data:image\/(.*?);base64,/', '', $request->input($string));
            $folder = $folder . "/" . Str::random() . "." . end($extension);

            $stored = Storage::put(
                $folder,
                base64_decode($image)
            );
        }

        if ($stored) {
            $image = str_replace("\\", "", $folder);
            $image = explode("/", $image);
            $image = end($image);
            return $image;
        }

        return null;
    }
}
