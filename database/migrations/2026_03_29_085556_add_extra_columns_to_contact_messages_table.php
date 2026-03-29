<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('contact_messages', 'phone')) {
                $table->string('phone', 50)->nullable()->after('email');
            }

            if (! Schema::hasColumn('contact_messages', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('message');
            }

            if (! Schema::hasColumn('contact_messages', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('is_read');
            }

            if (! Schema::hasColumn('contact_messages', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('read_at');
            }

            if (! Schema::hasColumn('contact_messages', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $columns = [
                'phone',
                'is_read',
                'read_at',
                'ip_address',
                'user_agent',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('contact_messages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
