<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Pocket;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LoginController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    private $pocketClient;

    /**
     * @param Pocket $pocketClient
     */
    public function __construct(Pocket $pocketClient)
    {
        $this->middleware('guest', ['except' => 'logout']);

        $this->pocketClient = $pocketClient;
    }

    /**
     * Show the application's login form.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm(Request $request)
    {
        $redirectUrl = $request->root() . '/login/callback';

        $connectUrl = $this->pocketClient->getConnectUrl($redirectUrl);

        return redirect($connectUrl);
    }

    public function pocketCallback(Request $request)
    {
        $requestToken = $request->input('request_token');

        if (empty($requestToken)) {
            throw new BadRequestHttpException('No request token specified.');
        }

        $accessToken = $this->pocketClient->getAccessToken($requestToken);

        // retrieves the user by email or creates a new one
        $user = User::firstOrNew(['email' => $accessToken['email']]);
        $user->pocket_access_token = $accessToken['pocketAccessToken'];
        $user->save();


        $this->guard()->login($user);

        return redirect('/home');
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect('/');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
