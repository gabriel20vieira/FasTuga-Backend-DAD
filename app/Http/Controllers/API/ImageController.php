<?php

namespace App\Http\Controllers\API;

use App\Traits\LoadsImages;
use App\Traits\StoresImages;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreImageRequest;

class ImageController extends Controller
{
    use StoresImages, LoadsImages;

    /**
     * Uploads image. Support for base64 and file
     *
     * @param StoreImageRequest $request
     * @return void
     */
    public function upload(StoreImageRequest $request)
    {
        Gate::authorize('upload-image');

        $path = $this->storeImage($request, $request->input('path'), 'image');

        $image = $path;
        $image = str_replace("\\", "", $path);
        $image = explode("/", $image);
        $image = end($image);


        return [
            'image' => $image,
            'path' => $path
        ];
    }

    /**
     * Searches for image instance
     *
     * @param string $image
     * @return void
     */
    public function show(string $image)
    {
        return $this->loadImage($image);
    }
}
