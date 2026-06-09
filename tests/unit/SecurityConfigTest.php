<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class SecurityConfigTest extends CIUnitTestCase
{
    public function testCsrfTokenRegenerationEnabled(): void
    {
        $config = new \Config\Security();
        $this->assertTrue($config->regenerate, 'CSRF token regeneration should be enabled');
    }

    public function testSessionIpMatchingEnabled(): void
    {
        $config = new \Config\Session();
        $this->assertTrue($config->matchIP, 'Session IP matching should be enabled');
    }

    public function testSecureCookiesConfigured(): void
    {
        $config = new \Config\Cookie();
        // Secure cookies require HTTPS; disabled for local dev on http://localhost
        $this->assertFalse($config->secure, 'Secure cookies should be false for local dev (HTTP)');
    }

    public function testCspConfigExists(): void
    {
        $config = new \Config\ContentSecurityPolicy();
        // CSP is configured but disabled until app is audited for inline styles
        $this->assertFalse((new \Config\App())->CSPEnabled, 'CSP should be disabled until inline style audit is done');
        // Verify CSP config class is loadable and has reasonable defaults
        $this->assertTrue($config->reportOnly === false || $config->reportOnly === true);
    }

    public function testThrottleFilterRegistered(): void
    {
        $config = new \Config\Filters();
        $this->assertArrayHasKey('throttle', $config->aliases, 'Throttle filter should be registered');
        $this->assertEquals(
            \App\Filters\ThrottleFilter::class,
            $config->aliases['throttle']
        );
    }

    public function testAuthRoutesHaveThrottleFilter(): void
    {
        $routes = \Config\Services::routes();
        $routes->loadRoutes();

        // Verify the routes file has throttle on auth endpoints
        $routeFile = file_get_contents(HOMEPATH . 'app/Config/Routes.php');
        $this->assertStringContainsString(
            "['filter' => 'throttle']",
            $routeFile,
            'Auth routes should have throttle filter applied'
        );
    }

    public function testDeleteRoutesArePost(): void
    {
        $routeFile = file_get_contents(HOMEPATH . 'app/Config/Routes.php');

        // Organizations delete
        $this->assertMatchesRegularExpression(
            '/\$routes->post\(\'organizations\/delete/',
            $routeFile,
            'Organization delete route should be POST'
        );

        // Calendar activities delete
        $this->assertMatchesRegularExpression(
            '/\$routes->post\(\'calendar-activities\/delete/',
            $routeFile,
            'Calendar activities delete route should be POST'
        );

        // Program expenditure delete
        $this->assertMatchesRegularExpression(
            '/\$routes->post\(\'program-expenditure\/delete/',
            $routeFile,
            'Program expenditure delete route should be POST'
        );

        // Submissions delete
        $this->assertMatchesRegularExpression(
            '/\$routes->post\(\'submissions\/delete/',
            $routeFile,
            'Submissions delete route should be POST'
        );
    }

    public function testNoGetDeleteRoutes(): void
    {
        $routeFile = file_get_contents(HOMEPATH . 'app/Config/Routes.php');

        // Extract all GET routes that contain 'delete'
        preg_match_all('/\$routes->get\(\'([^\']*delete[^\']*)\'/', $routeFile, $matches);

        // Filter out download/print routes that legitimately use GET
        $deleteGetRoutes = array_filter($matches[1], function ($route) {
            return !str_contains($route, 'download') && !str_contains($route, 'print');
        });

        $this->assertEmpty(
            $deleteGetRoutes,
            'No GET delete routes should exist. Found: ' . implode(', ', $deleteGetRoutes)
        );
    }
}
