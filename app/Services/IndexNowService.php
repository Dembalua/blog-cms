<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IndexNowService
{
    public function isEnabled(): bool
    {
        return filled(config('services.indexnow.key'));
    }

    public function publishKeyFile(): void
    {
        $key = config('services.indexnow.key');
        $stateFile = storage_path('app/indexnow-published-key.txt');
        $previous = is_file($stateFile) ? trim((string) file_get_contents($stateFile)) : null;

        if ($previous && $previous !== $key) {
            $previousPath = public_path($previous.'.txt');
            if (is_file($previousPath)) {
                unlink($previousPath);
            }
        }

        if (! $this->isEnabled()) {
            if (is_file($stateFile)) {
                unlink($stateFile);
            }

            return;
        }

        file_put_contents(public_path($key.'.txt'), $key);
        file_put_contents($stateFile, $key);
    }

    public function submit(array $urls): bool
    {
        if (! $this->isEnabled() || empty($urls)) {
            return false;
        }

        $key = config('services.indexnow.key');
        $endpoint = config('services.indexnow.endpoint');
        $host = parse_url(config('app.url'), PHP_URL_HOST);
        $keyLocation = rtrim(config('app.url'), '/').'/'.$key.'.txt';

        $response = Http::timeout(15)->acceptJson()->post($endpoint, [
            'host' => $host,
            'key' => $key,
            'keyLocation' => $keyLocation,
            'urlList' => array_values($urls),
        ]);

        if (! $response->successful()) {
            Log::warning('IndexNow submit failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'count' => count($urls),
            ]);

            return false;
        }

        return true;
    }
}
