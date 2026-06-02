<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class AuthValidationTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        $this->resetConfig();
    }

    protected function tearDown(): void
    {
        $this->resetConfig();
    }

    protected function resetConfig(): void
    {
        // Reset any singleton instances
    }

    public function testPasswordMinLengthInLoginValidation(): void
    {
        $controller = new \App\Controllers\Auth();
        $routeFile = file_get_contents(HOMEPATH . 'app/Controllers/Auth.php');

        // Check login validation uses min_length[8]
        $this->assertStringContainsString(
            'min_length[8]',
            $routeFile,
            'Login password validation should require min_length[8]'
        );
    }

    public function testPasswordMinLengthInChangePasswordValidation(): void
    {
        $routeFile = file_get_contents(HOMEPATH . 'app/Controllers/Auth.php');

        // Count occurrences of min_length[8] - should be at least 3
        // (login, change password, registration)
        $count = substr_count($routeFile, 'min_length[8]');
        $this->assertGreaterThanOrEqual(
            3,
            $count,
            'min_length[8] should appear in at least 3 password validation rules'
        );
    }

    public function testNoMinLength6InAuth(): void
    {
        $routeFile = file_get_contents(HOMEPATH . 'app/Controllers/Auth.php');

        // Extract validation rule lines only (not error messages)
        preg_match_all('/rules.*?min_length\[(\d+)\]/', $routeFile, $matches);

        foreach ($matches[1] as $length) {
            $this->assertNotEquals(
                '6',
                $length,
                'No password validation rule should use min_length[6], found: min_length[' . $length . ']'
            );
        }
    }

    public function testAdviserSignatureValidatesBase64(): void
    {
        $routeFile = file_get_contents(HOMEPATH . 'app/Controllers/Auth.php');

        $this->assertStringContainsString(
            'preg_match',
            $routeFile,
            'Adviser signature should validate base64 format'
        );

        $this->assertStringContainsString(
            'getimagesizefromstring',
            $routeFile,
            'Adviser signature should validate image data'
        );
    }

    public function testAdviserSignatureFixesDirectoryPermissions(): void
    {
        $routeFile = file_get_contents(HOMEPATH . 'app/Controllers/Auth.php');

        // Should use 0755, not 0777
        $this->assertStringNotContainsString(
            "mkdir(\$uploadDir, 0777",
            $routeFile,
            'Signature upload directory should not use 0777 permissions'
        );

        $this->assertStringContainsString(
            "mkdir(\$uploadDir, 0755",
            $routeFile,
            'Signature upload directory should use 0755 permissions'
        );
    }
}
