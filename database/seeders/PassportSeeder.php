<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;

class PassportSeeder extends Seeder
{
    /**
     * Seed the Passport clients used for API authentication.
     */
    public function run(): void
    {
        $provider = config('auth.guards.api.provider', 'users');
        $clients = app(ClientRepository::class);

        if (! $this->hasGrantClient('personal_access', $provider)) {
            $clients->createPersonalAccessGrantClient(
                config('app.name').' Personal Access Client',
                $provider,
            );
        }

        if (! $this->hasGrantClient('password', $provider)) {
            $clients->createPasswordGrantClient(
                config('app.name').' Password Grant Client',
                $provider,
            );
        }
    }

    protected function hasGrantClient(string $grantType, string $provider): bool
    {
        return Passport::client()
            ->newQuery()
            ->where('revoked', false)
            ->get()
            ->contains(fn (Client $client) => $client->hasGrantType($grantType)
                && (is_null($client->provider) || $client->provider === $provider));
    }
}
