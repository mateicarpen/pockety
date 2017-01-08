<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** @var User */
    protected $currentUser;

    public function __construct(AuthManager $authManager)
    {
        // have to initialize this in the middleware, as otherwise the auth middleware may not have run yet.
        $this->middleware(function ($request, $next) use ($authManager) {
            $this->currentUser = $authManager->guard('web')->user();
            return $next($request);
        });
    }
}
