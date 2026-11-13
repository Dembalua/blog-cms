<?php

namespace App\Console\Commands;

use App\Services\IndexNowService;
use Illuminate\Console\Command;

class PublishIndexNowKey extends Command
{
    protected $signature = 'indexnow:publish-key';

    protected $description = 'Write the IndexNow key file to public/ (and remove the previous one on rotation)';

    public function handle(IndexNowService $service): int
    {
        $service->publishKeyFile();

        if (! $service->isEnabled()) {
            $this->info('IndexNow disabled (no INDEXNOW_KEY) — any previous key file was removed.');

            return self::SUCCESS;
        }

        $key = config('services.indexnow.key');
        $this->info('Published key file: '.public_path($key.'.txt'));

        return self::SUCCESS;
    }
}
