<?php

namespace App\Console\Commands;

use App\Mail\BrokenAffiliateLinks;
use App\Models\AffiliateLink;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CheckAffiliateLinks extends Command
{
    protected $signature = 'affiliate-links:check';

    protected $description = 'Check all active affiliate links via HTTP and store the status code';

    public function handle(): void
    {
        $links = AffiliateLink::where('is_active', true)->get();

        $this->info("Checking {$links->count()} affiliate link(s)...");

        $broken = collect();

        foreach ($links as $link) {
            try {
                $response   = Http::timeout(10)->head($link->url);
                $statusCode = $response->status();

                if ($statusCode === 405) {
                    $response   = Http::timeout(10)->get($link->url);
                    $statusCode = $response->status();
                }
            } catch (\Throwable) {
                $statusCode = 0;
            }

            $link->update([
                'last_status_code' => $statusCode,
                'last_checked_at'  => now(),
            ]);

            if ($statusCode < 200 || $statusCode >= 300) {
                $broken->push($link);
                $this->warn("  [{$statusCode}] {$link->name} — {$link->url}");
            } else {
                $this->line("  [{$statusCode}] {$link->name}");
            }
        }

        if ($broken->isNotEmpty()) {
            Mail::to(config('app.log_mail_to'))->send(new BrokenAffiliateLinks($broken));
        }

        $this->info("Done. {$broken->count()} error(s) found.");
    }
}
