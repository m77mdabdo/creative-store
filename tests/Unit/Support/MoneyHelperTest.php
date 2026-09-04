<?php

namespace Tests\Unit\Support;

use Tests\TestCase;

class MoneyHelperTest extends TestCase
{
    public function test_prices_use_the_currency_label_of_the_current_language(): void
    {
        app()->setLocale('en');
        $this->assertSame('1,289.50 SAR', money(1289.5));

        app()->setLocale('ar');
        $this->assertSame('1,289.50 ر.س', money('1289.5', 'SAR'));
        $this->assertSame('10.00 USD', money(10, 'USD'));
    }

    public function test_status_labels_are_translated(): void
    {
        app()->setLocale('en');
        $this->assertSame('Partially Refunded', status_label('partially_refunded'));
        $this->assertSame('—', status_label(null));
    }
}
