<?php

namespace App\Notifications\Concerns;

/**
 * Queued notifications still land in the dashboard bell the moment they are
 * sent: only the slow channels (mail, SMS/WhatsApp) wait for the queue
 * worker, while the database row is written right away.
 */
trait WritesBellImmediately
{
    public function viaConnections(): array
    {
        return ['database' => 'sync'];
    }
}
