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
