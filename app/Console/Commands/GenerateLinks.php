<?php

namespace App\Console\Commands;

use App\Models\Link;
use Illuminate\Console\Command;
use Hidehalo\Nanoid\Client;
use Illuminate\Process\Pool;
use Illuminate\Support\Facades\Process;

class GenerateLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-links {--processes=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new Client();

        $processes = (int) $this->option('processes');

        if($processes)
        {
            return $this->spawn($processes);
        }

        for($j = 0; $j < 10; $j++)
        {
            echo ".";

            $links = [];

            for($i = 0; $i < 1000; $i++)
            {
                array_push($links, [
                    'short_id' => $client->generateId(8),
                    'long_url' => \Illuminate\Support\Str::random(10),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            foreach(array_chunk($links, 300) as $chunk)
            {
                Link::insert($chunk);
            }

            unset($links);
        }

        $this->newLine();
        $this->info('Database seeding complete.');
    }

    public function spawn(int $processes)
    {
        Process::pool(function(Pool $pool) use($processes) {
            for($i=0; $i < $processes; $i++)
            {
                $pool->command('php artisan generate-links')->timeout(60*5);
            }
        })->start()->wait();
    }
}
