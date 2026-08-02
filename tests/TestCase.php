<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // RefreshDatabase migrates from scratch, so pointing it at anything
        // other than the throwaway sqlite database would drop real tables.
        // A real DB_CONNECTION in the environment beats phpunit.xml, which is
        // exactly the situation inside the app container.
        $connection = config('database.default');
        if ($connection !== 'sqlite') {
            throw new RuntimeException(
                "Refusing to run tests against the '{$connection}' connection. "
                . 'Tests expect the in-memory sqlite database configured in '
                . 'phpunit.xml; unset DB_CONNECTION in the environment.'
            );
        }
    }
}
