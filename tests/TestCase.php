<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Localized routes take their prefix from the URL when the app boots.
     * Tests boot without a request, so pin the routing locale to English;
     * URLs then resolve as /en/... and route() builds them correctly.
     */
    public function createApplication()
    {
        putenv('ROUTING_LOCALE=en');
        $_ENV['ROUTING_LOCALE'] = $_SERVER['ROUTING_LOCALE'] = 'en';

        return parent::createApplication();
    }
}
