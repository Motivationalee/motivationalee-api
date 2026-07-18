<?php

namespace App\Services\Auth;

use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;

class OAuthTokenService
{
    public function __construct(
        protected AuthorizationServer $server,
    ) {
    }

    /**
     * Issue access and refresh tokens using the password grant.
     *
     * @return array{token_type: string, expires_in: int, access_token: string, refresh_token: string}
     */
    public function issueWithPassword(string $email, string $password, string $scope = ''): array
    {
        return $this->dispatch([
            'grant_type' => 'password',
            'client_id' => $this->passwordClient()->getKey(),
            'username' => $email,
            'password' => $password,
            'scope' => $scope,
        ]);
    }

    /**
     * Issue a new token pair using a refresh token.
     *
     * @return array{token_type: string, expires_in: int, access_token: string, refresh_token: string}
     */
    public function issueWithRefreshToken(string $refreshToken, string $scope = ''): array
    {
        return $this->dispatch([
            'grant_type' => 'refresh_token',
            'client_id' => $this->passwordClient()->getKey(),
            'refresh_token' => $refreshToken,
            'scope' => $scope,
        ]);
    }

    /**
     * @param  array<string, string>  $data
     * @return array{token_type: string, expires_in: int, access_token: string, refresh_token: string}
     */
    protected function dispatch(array $data): array
    {
        $psrRequest = (new PsrHttpFactory)->createRequest(
            Request::create(config('app.url'), 'POST', $data)
        );

        $response = $this->server->respondToAccessTokenRequest(
            $psrRequest,
            app(ResponseInterface::class)
        );

        /** @var array{token_type: string, expires_in: int, access_token: string, refresh_token: string} $tokens */
        $tokens = json_decode($response->getBody()->__toString(), true);

        return $tokens;
    }

    protected function passwordClient(): Client
    {
        $provider = config('auth.guards.api.provider', 'users');

        $client = Passport::client()
            ->newQuery()
            ->where('revoked', false)
            ->latest()
            ->get()
            ->first(fn (Client $client): bool => $client->hasGrantType('password')
                && (is_null($client->provider) || $client->provider === $provider));

        return $client ?? throw new RuntimeException(
            "Password grant client not found for '{$provider}' user provider. Please seed Passport clients."
        );
    }
}
