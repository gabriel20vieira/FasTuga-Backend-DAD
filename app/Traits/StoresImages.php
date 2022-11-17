<?php

namespace App\Traits;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

trait StoresImages
{
    private function storeImage(FormRequest $request, $folder, $string)
    {
        $saved = "";
        if ($request->file($string)->isValid()) {
            if (!preg_match("/^\/.+/m", $folder)) {
                $folder = "/" . $folder;
            }

            $saved = Storage::putFile(
                'public' . $folder,
                $request->file($string)
            );
        }
        return $saved;
    }
}
