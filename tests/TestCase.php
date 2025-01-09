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
            ? 'http://localhost:8000' 
            : 'https://votre-domaine.com';
    }
} 