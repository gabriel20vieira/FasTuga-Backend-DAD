<?php

namespace App\Http\Controllers;

use App\Traits\APIHybridAuthentication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Undocumented function
     *
     * @param Builder $paginator
     * @return void
     */
    public function paginateBuilder(Builder $paginator, $paginate = null)
    {
        $paginator = $paginator->latest()->paginate($paginate ?? env('PAGINATE', 15))->withQueryString();

        if ($paginator->isEmpty() && !$paginator->onFirstPage()) {
            $this->redirect($paginator->url(1));
        }

        return $paginator;
    }

    /**
     * Redirects on the fly
     *
     * @param string $url
     * @return HttpResponseException
     */
    public function redirect(string $url)
    {
        throw new HttpResponseException(redirect($url));
    }
}
