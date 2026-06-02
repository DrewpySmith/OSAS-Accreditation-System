<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ThrottleFilter implements FilterInterface
{
    protected int $maxAttempts = 5;
    protected int $lockoutTime = 900;

    public function before(RequestInterface $request, $arguments = null)
    {
        $ip = $request->getIPAddress();
        $key = 'throttle_' . md5($ip . ($arguments[0] ?? 'global'));

        $cache = \Config\Services::cache();
        $attempts = (int) ($cache->get($key) ?? 0);

        if ($attempts >= $this->maxAttempts) {
            if ($request->isAJAX()) {
                return \Config\Services::response()->setJSON([
                    'success' => false,
                    'message' => 'Too many attempts. Please try again in 15 minutes.',
                    'csrf' => csrf_hash(),
                ], 429);
            }

            return \Config\Services::response()
                ->redirect()
                ->back()
                ->with('error', 'Too many attempts. Please try again in 15 minutes.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $uri = $request->getUri()->getPath();
        $authPaths = ['authenticate', 'forgot-password', 'reset-password', 'register'];
        $isAuthEndpoint = false;

        foreach ($authPaths as $path) {
            if (str_contains($uri, $path)) {
                $isAuthEndpoint = true;
                break;
            }
        }

        if (!$isAuthEndpoint) {
            return $response;
        }

        $isFailure = false;
        $statusCode = $response->getStatusCode();

        if ($statusCode === 302) {
            $location = $response->getHeaderLine('Location');
            if (str_contains($location, '/login')) {
                $isFailure = true;
            }
        } elseif ($statusCode === 401) {
            $isFailure = true;
        }

        if ($isFailure) {
            $ip = $request->getIPAddress();
            $key = 'throttle_' . md5($ip . ($arguments[0] ?? 'global'));

            $cache = \Config\Services::cache();
            $attempts = (int) ($cache->get($key) ?? 0);

            if ($attempts < $this->maxAttempts) {
                $cache->save($key, $attempts + 1, $this->lockoutTime);
            }
        } else {
            $ip = $request->getIPAddress();
            $key = 'throttle_' . md5($ip . ($arguments[0] ?? 'global'));
            $cache = \Config\Services::cache();
            $cache->delete($key);
        }

        return $response;
    }
}
