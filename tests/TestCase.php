<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected string $baseUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseUrl = getenv('APP_ENV') === 'local'
            ?  'https://samurang-portal.nqx.fr' : 'http://localhost:8000';
    }
}
