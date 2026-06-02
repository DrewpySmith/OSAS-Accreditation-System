<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response;

/**
 * @internal
 */
final class ThrottleFilterTest extends CIUnitTestCase
{
    protected \App\Filters\ThrottleFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new \App\Filters\ThrottleFilter();
    }

    public function testFilterClassExists(): void
    {
        $this->assertTrue(class_exists(\App\Filters\ThrottleFilter::class));
    }

    public function testFilterImplementsInterface(): void
    {
        $this->assertInstanceOf(\CodeIgniter\Filters\FilterInterface::class, $this->filter);
    }

    public function testFilterHasMaxAttemptsProperty(): void
    {
        $reflection = new ReflectionClass($this->filter);
        $prop = $reflection->getProperty('maxAttempts');
        $prop->setAccessible(true);
        $this->assertEquals(5, $prop->getValue($this->filter));
    }

    public function testFilterHasLockoutTimeProperty(): void
    {
        $reflection = new ReflectionClass($this->filter);
        $prop = $reflection->getProperty('lockoutTime');
        $prop->setAccessible(true);
        $this->assertEquals(900, $prop->getValue($this->filter));
    }

    public function testFilterMethodsExist(): void
    {
        $this->assertTrue(method_exists($this->filter, 'before'));
        $this->assertTrue(method_exists($this->filter, 'after'));
    }
}
