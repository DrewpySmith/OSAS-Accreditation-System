<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ControllerSecurityTest extends CIUnitTestCase
{
    public function testUpdateChecklistHasWhitelist(): void
    {
        $file = file_get_contents(HOMEPATH . 'app/Controllers/Admin/Organizations.php');

        $this->assertStringContainsString(
            '$allowedFields',
            $file,
            'updateChecklist should define $allowedFields'
        );

        $this->assertStringContainsString(
            "'application_letter'",
            $file,
            'allowedFields should include application_letter'
        );

        $this->assertStringContainsString(
            "'officer_list'",
            $file,
            'allowedFields should include officer_list'
        );

        $this->assertStringContainsString(
            "in_array(\$field, \$allowedFields)",
            $file,
            'updateChecklist should validate field against whitelist'
        );
    }

    public function testUpdateChecklistRejectsInvalidField(): void
    {
        $file = file_get_contents(HOMEPATH . 'app/Controllers/Admin/Organizations.php');

        // Verify the 400 response for invalid field
        $this->assertStringContainsString(
            "'Invalid field'",
            $file,
            'updateChecklist should return error for invalid field'
        );
    }

    public function testAccomplishmentReportFileValidation(): void
    {
        $file = file_get_contents(HOMEPATH . 'app/Controllers/Organization/AccomplishmentReport.php');

        // Check allowed MIME types are defined
        $this->assertStringContainsString(
            "'image/jpeg'",
            $file,
            'Should allow JPEG uploads'
        );

        $this->assertStringContainsString(
            "'image/png'",
            $file,
            'Should allow PNG uploads'
        );

        $this->assertStringContainsString(
            "'image/gif'",
            $file,
            'Should allow GIF uploads'
        );

        $this->assertStringContainsString(
            "'application/pdf'",
            $file,
            'Should allow PDF uploads'
        );

        // Check size validation
        $this->assertStringContainsString(
            'getSize()',
            $file,
            'Should validate file size'
        );

        $this->assertStringContainsString(
            'getMimeType()',
            $file,
            'Should validate MIME type'
        );
    }

    public function testFinancialReportPassbookValidation(): void
    {
        $file = file_get_contents(HOMEPATH . 'app/Controllers/Organization/FinancialReport.php');

        $this->assertStringContainsString(
            "'image/jpeg'",
            $file,
            'Passbook should allow JPEG'
        );

        $this->assertStringContainsString(
            "'application/pdf'",
            $file,
            'Passbook should allow PDF'
        );

        $this->assertStringContainsString(
            'getSize()',
            $file,
            'Passbook should validate file size'
        );
    }

    public function testAccomplishmentReportDirectoryPermissions(): void
    {
        $file = file_get_contents(HOMEPATH . 'app/Controllers/Organization/AccomplishmentReport.php');

        // Should not have 0777
        $this->assertStringNotContainsString(
            '0777',
            $file,
            'Accomplishment report should not use 0777 directory permissions'
        );

        $this->assertStringContainsString(
            '0755',
            $file,
            'Accomplishment report should use 0755 directory permissions'
        );
    }
}
