<?php

namespace App\Console\Commands;

use App\Jobs\ProcessAbandonedCarts;
use Illuminate\Console\Command;

class ProcessAbandonedCartsCommand extends Command
{
    protected $signature = 'carts:process-abandoned';
    protected $description = 'Process abandoned carts and send recovery emails';

    public function handle(): int
    {
        ProcessAbandonedCarts::dispatchSync();

        $this->info('Abandoned carts processed successfully.');

        return self::SUCCESS;
    }
}
