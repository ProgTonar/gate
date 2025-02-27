<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Laravel\Passport\Http\Controllers\AuthorizationController as BaseAuthorizationController;
use Psr\Http\Message\ServerRequestInterface;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\TokenRepository;
use Illuminate\Auth\AuthManager;
use Laravel\Passport\Contracts\AuthorizationViewResponse;
use Illuminate\Support\Str;
use League\OAuth2\Server\AuthorizationServer;

class CustomPKCE extends BaseAuthorizationController
{
    protected $guard;

    public function __construct(AuthorizationServer $server, AuthManager $auth, AuthorizationViewResponse $response)
    {
        $guard = $auth->guard();
        parent::__construct($server, $guard, $response);

        $this->guard = $guard;
    }

    public function authorize(ServerRequestInterface $psrRequest, Request $request, ClientRepository $clients, TokenRepository $tokens)
    {
        $authRequest = $this->withErrorHandling(function () use ($psrRequest) {
            return $this->server->validateAuthorizationRequest($psrRequest);
        });

        if ($this->guard->guest()) {
            return $request->get('prompt') === 'none'
                ? $this->denyRequest($authRequest)
                : $this->promptForLogin($request);
        }

        if ($request->get('prompt') === 'login' &&
            !$request->session()->get('promptedForLogin', false)) {
            $this->guard->logout();
            $request->session()->regenerateToken();

            return $this->promptForLogin($request);
        }

        $request->session()->forget('promptedForLogin');

        $scopes = $this->parseScopes($authRequest);
        $user = $this->guard->user();
        $client = $clients->find($authRequest->getClient()->getIdentifier());

        if ($request->get('prompt') === 'blind') {
            $savedSessionData = [
                'state' => session()->get('state'),
                'codeVerifier' => session()->get('codeVerifier'),
                'name' => session()->get('name'),
            ];

            $this->guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            session()->put($savedSessionData);

            return $this->approveRequest($authRequest, $user);
        }

        if ($request->get('prompt') !== 'consent' &&
            ($client->skipsAuthorization() || $this->hasValidToken($tokens, $user, $client, $scopes))) {
            return $this->approveRequest($authRequest, $user);
        }

        if ($request->get('prompt') === 'none') {
            return $this->denyRequest($authRequest, $user);
        }

        $request->session()->put('authToken', $authToken = Str::random());
        $request->session()->put('authRequest', $authRequest);

        return $this->response->withParameters([
            'client' => $client,
            'user' => $user,
            'scopes' => $scopes,
            'request' => $request,
            'authToken' => $authToken,
        ]);
    }
}
