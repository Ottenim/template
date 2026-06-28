<?php

namespace Template\LandingCore\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AssignRequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $this->headerName();
        $requestId = $this->resolveRequestId($request, $header);

        Context::add('request_id', $requestId);

        $response = $next($request);
        $response->headers->set($header, $requestId);

        return $response;
    }

    protected function headerName(): string
    {
        $header = trim((string) config('landing-core.observability.request_id.header', 'X-Request-Id'));

        return $header === '' ? 'X-Request-Id' : $header;
    }

    protected function resolveRequestId(Request $request, string $header): string
    {
        $incoming = trim((string) $request->headers->get($header, ''));

        // Allowlist: só reaproveita o id recebido se for seguro para log/header.
        if ($incoming !== '' && preg_match('/^[A-Za-z0-9._-]{1,128}$/', $incoming)) {
            return $incoming;
        }

        return (string) Str::uuid();
    }
}
