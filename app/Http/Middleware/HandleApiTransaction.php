<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HandleApiTransaction
{
    /**
     * Wrap every API request in a database transaction with try/catch handling.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        DB::beginTransaction();

        try {
            $response = $next($request);

            if ($this->shouldCommit($response)) {
                DB::commit();
            } else {
                info($response);
                DB::rollBack();

                $payload = json_decode($response->getContent(), true);
                $jsonResponse = [
                    'message' => $payload['message'] ?? 'Transaction failed. Please try again.',
                ];

                if(isset($payload['errors'])) $jsonResponse['errors'] = $payload['errors'];

                return response()->json($jsonResponse, $response->getStatusCode());
            }

            return $response;
        } catch (Throwable $e) {
            info($e);
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            throw $e;
        }
    }

    /**
     * Commit only successful and redirect responses.
     */
    protected function shouldCommit(Response $response): bool
    {
        return $response->isSuccessful() || $response->isRedirection();
    }
}
