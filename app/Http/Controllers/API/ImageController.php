<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImageRequest;
use App\Traits\StoresImages;

class ImageController extends Controller
{
    use StoresImages;

    public function upload(StoreImageRequest $request)
    {
        return [
            'image' => $this->storeImage($request, $request->input('path'), 'image')
        ];
    }
}
