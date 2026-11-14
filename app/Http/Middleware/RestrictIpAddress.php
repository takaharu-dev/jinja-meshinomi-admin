<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * config/admin.php の許可 IP 以外からのアクセスを 403 にする。
 */
class RestrictIpAddress
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('admin.ip_restriction.enabled')) {
            return $next($request);
        }

        /** @var list<string> $allowedIps */
        $allowedIps = config('admin.ip_restriction.allowed_ips');
        $ip = $request->ip();

        if ($ip === null || ! IpUtils::checkIp($ip, $allowedIps)) {
            Log::warning('許可されていない IP からのアクセスを拒否しました', [
                'ip' => $ip,
                'path' => $request->path(),
            ]);

            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
