<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $accept = (string) $request->getHeaderLine('Accept');
        $isHtmlRequest = ($accept === '' || stripos($accept, 'text/html') !== false);
        $isAjax = method_exists($request, 'isAJAX') && $request->isAJAX();

        // Check if this is a preview endpoint request (iframe loads or PDF.js)
        $uri = $request->getUri();
        $isPreviewRequest = strpos($uri->getPath(), '/admin/documents/preview/') !== false;

        // Treat preview requests as non-HTML to avoid redirects
        if ($isPreviewRequest) {
            $isHtmlRequest = false;
            $isAjax = false; // Don't treat as AJAX either
        }

        if (!session()->get('logged_in')) {
            if ($isAjax || !$isHtmlRequest) {
                return service('response')
                    ->setStatusCode(401)
                    ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
                    ->setBody('Unauthorized');
            }

            return redirect()->to('/login')->with('error', 'Please login first');
        }

        if (session()->get('role') !== 'admin') {
            if ($isAjax || !$isHtmlRequest) {
                return service('response')
                    ->setStatusCode(403)
                    ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
                    ->setBody('Forbidden');
            }

            return redirect()->to('/organization/dashboard')->with('error', 'Access denied');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}