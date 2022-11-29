<?php

namespace App\Http\Controllers\API;

use App\Traits\StoresImages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreImageRequest;
use App\Traits\LoadsImages;

class ImageController extends Controller
{
    use StoresImages, LoadsImages;

    public function upload(StoreImageRequest $request)
    {
        $path = $this->storeImage($request, $request->input('path'), 'image');

        $image = $path; {
            $image = str_replace("\\", "", $path);
            $image = explode("/", $image);
            $image = end($image);
        }

        return [
            'image' => $image,
            'path' => $path
        ];
    }

    public function show($image)
    {
        return $this->loadImage($image);
    }
}
