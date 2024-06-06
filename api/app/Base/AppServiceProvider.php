<?php

namespace App\Base;

use App\Service\AuthorizationService;
use App\Service\SessionService;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends BaseServiceProvider
{
    /**
     * Define your service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->viaRequest('custom', function ($request) {
            $sessionKey = $request->cookie(SessionService::SESSION_KEY);
            $session = app(SessionService::class)->restore($sessionKey);
            if ($session === null) {
                return;
            }
            app(AuthorizationService::class)->authorize($session->role_id, $request->path());
            return new User(
                $session->user_id,
                $session->department_id,
            );
        });

        Builder::macro('exSearch', function (SearchOption $searcher) {
            return $searcher->getResults($this);
        });

        Request::macro('validate', function (array $rules, ...$params) {
            return validator()->validate($this->all(), $rules, ...$params);
        });

        $this->routes(function () {
            require base_path('../app/routes.php');
        });
    }
}
