<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Models\User;
use App\Services\Auth\OAuthTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Passport;
use League\OAuth2\Server\Exception\OAuthServerException;

class AuthController extends Controller
{
    public function __construct(
        protected OAuthTokenService $tokens,
    ) {
    }

    /**
     * Authenticate an existing user and issue access + refresh tokens.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->ensureIsNotRateLimited();

        try {
            $tokenResponse = $this->tokens->issueWithPassword(
                $request->string('email')->toString(),
                $request->string('password')->toString(),
            );
        } catch (OAuthServerException) {
            $request->hitRateLimiter();

            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $request->clearRateLimiter();

        $user = User::query()
            ->where('email', $request->string('email')->toString())
            ->firstOrFail();

        return response()->json([
            'token_type' => $tokenResponse['token_type'],
            'expires_in' => $tokenResponse['expires_in'],
            'access_token' => $tokenResponse['access_token'],
            'refresh_token' => $tokenResponse['refresh_token'],
            'user' => $user,
        ]);
    }

    /**
     * Exchange a refresh token for a new access + refresh token pair.
     */
    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        try {
            $tokenResponse = $this->tokens->issueWithRefreshToken(
                $request->string('refresh_token')->toString(),
            );
        } catch (OAuthServerException) {
            return response()->json([
                'message' => 'Invalid or expired refresh token.',
            ], 401);
        }

        return response()->json([
            'token_type' => $tokenResponse['token_type'],
            'expires_in' => $tokenResponse['expires_in'],
            'access_token' => $tokenResponse['access_token'],
            'refresh_token' => $tokenResponse['refresh_token'],
        ]);
    }

    /**
     * Get the authenticated user.
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the authenticated user's password.
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->password = $request->string('password')->toString();
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully.',
        ]);
    }

    /**
     * Revoke the current access token and its refresh token.
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $accessTokenId = $token->oauth_access_token_id ?? $token->id;

            Passport::refreshToken()
                ->newQuery()
                ->where('access_token_id', $accessTokenId)
                ->update(['revoked' => true]);

            $token->revoke();
        }

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
