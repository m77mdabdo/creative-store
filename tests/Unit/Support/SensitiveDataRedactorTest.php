<?php

namespace Tests\Unit\Support;

use App\Support\Logging\SensitiveDataRedactor;
use Illuminate\Database\QueryException;
use PDOException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SensitiveDataRedactorTest extends TestCase
{
    public function test_personal_data_and_secrets_are_masked(): void
    {
        $text = 'Login failed for sara@example.com from 0551234567 / +966551234567 '
            . 'password=Secret123 token: abcdef123456 card 4111 1111 1111 1111 Bearer eyJhbGciOiJIUzI1NiJ9.x';

        $clean = SensitiveDataRedactor::redact($text);

        foreach (['sara@example.com', '0551234567', '+966551234567', 'Secret123', 'abcdef123456', '4111 1111 1111 1111', 'eyJhbGci'] as $secret) {
            $this->assertStringNotContainsString($secret, $clean);
        }

        $this->assertStringContainsString('[email]', $clean);
        $this->assertStringContainsString('password=[redacted]', $clean);
    }

    public function test_query_errors_keep_the_sql_but_not_the_values(): void
    {
        $pdo = new PDOException('SQLSTATE[23000]: Integrity constraint violation');
        $pdo->errorInfo = ['23000', 1062, "Duplicate entry for key 'users_email_unique'"];

        $e = new QueryException('mysql', 'insert into users (email, phone) values (?, ?)', ['omar@example.com', '0559876543'], $pdo);

        $message = SensitiveDataRedactor::message($e);

        $this->assertStringContainsString('insert into users (email, phone) values (?, ?)', $message);
        $this->assertStringNotContainsString('omar@example.com', $message);
        $this->assertStringNotContainsString('0559876543', $message);
    }

    public function test_ordinary_messages_pass_through(): void
    {
        $this->assertSame('Undefined index: total', SensitiveDataRedactor::message(new RuntimeException('Undefined index: total')));
        $this->assertSame('RuntimeException', SensitiveDataRedactor::message(new RuntimeException('')));
    }
}
