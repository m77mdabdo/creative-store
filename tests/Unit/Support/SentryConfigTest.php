<?php

namespace Tests\Unit\Support;

use Tests\TestCase;

class SentryConfigTest extends TestCase
{
    public function test_sentry_is_off_without_a_dsn_and_never_sends_personal_data(): void
    {
        $this->assertEmpty(config('sentry.dsn'));
        $this->assertFalse(config('sentry.send_default_pii'));
    }
}
