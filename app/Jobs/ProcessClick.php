<?php

namespace App\Jobs;

use App\Models\Link;
use App\Models\Visit;
use DeviceDetector\DeviceDetector;
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
        public Link $link,
        public string $userAgent,
        public string $ip,
        public int | null $choiceId = null
    )
    { }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $dd = new DeviceDetector($this->userAgent);
        $dd->parse();

        if($dd->isBot())
        {
            return;
        }

        $this->link->visits()->save(new Visit([
            'link_choice_id' => $this->choiceId,
            'ip' => $this->ip,
            'country' => $this->getCountry(),
            'device' => ucfirst($dd->getDeviceName()),
            'browser' => optional($dd->getClient())['name'],
            'os' => optional($dd->getOs())['name']
        ]));
    }

    private function getCountry(): string | null
    {
        $action = app(config('lynx.location_action'), ['ip' => $this->ip]);

        return $action->getCountry();
    }
}
