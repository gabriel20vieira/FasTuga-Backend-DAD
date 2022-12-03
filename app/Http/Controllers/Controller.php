<?php

namespace App\Http\Controllers;

use App\Traits\APIHybridAuthentication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Relations\Relation;
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
     * @param Builder|Relation $paginator
     * @return void
     */
    public function paginateBuilder(Builder|Relation $builder, $paginate = null)
    {
        // if ($builder instanceof Relation) {
        //     $builder = $builder->query();
        // }

        $builder = $builder->latest()->paginate($paginate ?? env('PAGINATE', 15))->withQueryString();

        if ($builder->isEmpty() && !$builder->onFirstPage()) {
            $this->redirect($builder->url(1));
        }

        return $builder;
    }

    /**
     * Redirects on the fly
     *
     * @param string $url
     * @return HttpResponseException
     */
    public function redirect(string $url)
    {
        $this->sendResponseNow(redirect($url));
    }

    /**
     * Sends response to client on call
     *
     * @param Response $response
     * @return void
     */
    public function sendResponseNow(Response $response)
    {
        throw new HttpResponseException($response);
    }

    /**
     * Sends response on condition
     *
     * @param boolean $condition
     * @param Response $response
     * @return void
     */
    public function sendResponseNowIf(bool $condition, Response $response)
    {
        if ($condition) {
            $this->sendResponseNow($response);
        }
    }
}
