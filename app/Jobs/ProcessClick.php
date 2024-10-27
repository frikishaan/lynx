<?php

namespace App\Jobs;

use App\Models\Link;
use App\Models\Visit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessClick implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Link $link
    )
    { }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->link->visits()->save(new Visit([
            'ip' => request()->ip()
        ]));
    }
}
