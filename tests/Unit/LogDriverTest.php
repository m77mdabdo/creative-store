<?php

namespace Tests\Unit;

use App\Services\Messaging\Drivers\LogDriver;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class LogDriverTest extends TestCase
{
    public function test_the_log_never_contains_the_full_number_or_the_text(): void
    {
        Log::spy();

        (new LogDriver())->send('+966551234567', 'Your verification code is 482913');

        Log::shouldHaveReceived('info')->once()->withArgs(function (string $message, array $context) {
            $logged = json_encode($context);

            return ! str_contains($logged, '482913')
                && ! str_contains($logged, '551234567')
                && $context['to'] === '+9665******67';
        });
    }
}
