<?php

namespace Tests\Unit\Zatca;

use App\Support\Zatca\ZatcaQr;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ZatcaQrTest extends TestCase
{
    public function test_it_encodes_the_five_tlv_fields(): void
    {
        $payload = ZatcaQr::payload(
            'مؤسسة كرييتف ستور',
            '300000000000003',
            CarbonImmutable::parse('2026-09-04 13:30:00', 'Asia/Riyadh'),
            1150.0,
            150.0,
        );

        $fields = ZatcaQr::decode($payload);

        $this->assertSame('مؤسسة كرييتف ستور', $fields[1]);
        $this->assertSame('300000000000003', $fields[2]);
        $this->assertSame('2026-09-04T10:30:00Z', $fields[3]);
        $this->assertSame('1150.00', $fields[4]);
        $this->assertSame('150.00', $fields[5]);
    }

    public function test_lengths_are_counted_in_utf8_bytes(): void
    {
        $payload = ZatcaQr::payload('متجر', '300000000000003', CarbonImmutable::now(), 10, 1.3);
        $binary = base64_decode($payload);

        // Four Arabic letters are eight bytes in UTF-8.
        $this->assertSame(1, ord($binary[0]));
        $this->assertSame(8, ord($binary[1]));
    }

    public function test_it_rejects_an_invalid_vat_number(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ZatcaQr::payload('Store', '123', CarbonImmutable::now(), 10, 1.5);
    }
}
