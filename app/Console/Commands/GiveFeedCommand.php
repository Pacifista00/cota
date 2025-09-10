<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GiveFeedCommand extends Command
{
    protected $signature = 'feed:give';
    protected $description = 'Kirim request ke API beri-pakan';

    public function handle()
    {
        try {
            $response = Http::get("http://192.168.18.89/beri-pakan");

            if ($response->successful()) {
                $this->info("✅ Pakan berhasil diberikan!");
            } else {
                $this->error("❌ Gagal beri pakan (Status: {$response->status()})");
            }
        } catch (\Exception $e) {
            $this->error("⚠️ Error: " . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
