<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    Setting,
    Page,
    PageTranslation,
    AuditLog,
    ErrorLog,
    RateLimitLog,
    DailySalesAggregate,
    SystemBackup,
    DataRetentionPolicy,
    PrivacyRequest,
    UatRun,
    UatRunItem,
    User
};

class OpsSeeder extends Seeder
{
    public function run(): void
    {
        /* =========================
         * Settings
         * ========================= */
        $settings = [
            ['key' => 'site_name', 'value' => 'Demo Store', 'type' => 'string'],
            ['key' => 'currency', 'value' => 'SAR', 'type' => 'string'],
            ['key' => 'tax_rate', 'value' => '15', 'type' => 'number'],
            ['key' => 'support_email', 'value' => 'support@demo.com', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        /* =========================
         * CMS Pages
         * ========================= */
        $pages = [
            ['slug' => 'about-us'],
            ['slug' => 'privacy-policy'],
            ['slug' => 'terms-and-conditions'],
        ];

        foreach ($pages as $pageData) {
            $page = Page::firstOrCreate($pageData);

            PageTranslation::firstOrCreate([
                'page_id' => $page->id,
                'locale' => 'en',
            ], [
                'title' => ucfirst(str_replace('-', ' ', $page->slug)),
                'content' => fake()->paragraph(5),
            ]);

            PageTranslation::firstOrCreate([
                'page_id' => $page->id,
                'locale' => 'ar',
            ], [
                'title' => 'صفحة ' . $page->slug,
                'content' => fake()->paragraph(5),
            ]);
        }

        /* =========================
         * Audit Logs
         * ========================= */
        if (User::count() > 0) {
            AuditLog::factory()->count(10)->create([
                'user_id' => User::inRandomOrder()->first()->id,
                'action' => 'update',
                'auditable_type' => 'Product',
                'auditable_id' => 1,
                'ip_address' => '127.0.0.1',
            ]);
        }

        /* =========================
         * Error Logs
         * ========================= */
        ErrorLog::factory()->count(5)->create([
            'level' => 'error',
            'service' => 'api',
            'message' => 'Sample error message',
        ]);

        /* =========================
         * Rate Limit Logs
         * ========================= */
        RateLimitLog::factory()->count(5)->create([
            'key' => 'ip:127.0.0.1',
            'endpoint' => '/api/products',
            'hits' => rand(10, 100),
            'window_start' => now()->subMinutes(5),
        ]);

        /* =========================
         * Daily Sales Aggregates
         * ========================= */
        for ($i = 0; $i < 7; $i++) {
            DailySalesAggregate::factory()->create([
                'day' => now()->subDays($i)->toDateString(),
            ]);
        }

        /* =========================
         * System Backups
         * ========================= */
        SystemBackup::factory()->count(3)->create([
            'type' => 'scheduled',
            'status' => 'success',
            'started_at' => now()->subHours(2),
            'finished_at' => now()->subHour(),
        ]);

        /* =========================
         * Data Retention Policies
         * ========================= */
        $policies = [
            ['target_table' => 'audit_logs', 'retain_days' => 365],
            ['target_table' => 'error_logs', 'retain_days' => 180],
            ['target_table' => 'rate_limit_logs', 'retain_days' => 30],
        ];

        foreach ($policies as $policy) {
            DataRetentionPolicy::firstOrCreate(
                ['target_table' => $policy['target_table']],
                $policy
            );
        }

        /* =========================
         * Privacy Requests
         * ========================= */
        if (User::count() > 0) {
            PrivacyRequest::factory()->count(3)->create([
                'user_id' => User::inRandomOrder()->first()->id,
                'type' => 'export',
                'status' => 'completed',
                'requested_at' => now()->subDays(2),
                'processed_at' => now()->subDay(),
            ]);
        }

        /* =========================
         * UAT Runs
         * ========================= */
        $uatRun = UatRun::factory()->create([
            'name' => 'Checkout Flow',
            'environment' => 'staging',
            'status' => 'passed',
            'started_at' => now()->subMinutes(30),
            'finished_at' => now(),
        ]);

        UatRunItem::factory()->count(4)->create([
            'uat_run_id' => $uatRun->id,
        ]);
    }
}
