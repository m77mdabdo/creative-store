<?php

namespace Tests\Unit;

use App\Support\Export\CsvCell;
use PHPUnit\Framework\TestCase;

class CsvCellTest extends TestCase
{
    public function test_formulas_are_turned_into_text(): void
    {
        $this->assertSame("'=HYPERLINK(\"x\")", CsvCell::safe('=HYPERLINK("x")'));
        $this->assertSame("'+1", CsvCell::safe('+1'));
        $this->assertSame("'@SUM(A1)", CsvCell::safe('@SUM(A1)'));
    }

    public function test_ordinary_values_are_unchanged(): void
    {
        $this->assertSame(['Sara', 42, null, 'sara@example.com'], CsvCell::row(['Sara', 42, null, 'sara@example.com']));
    }
}
