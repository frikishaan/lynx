<?php

namespace App\Actions;

use App\Abstracts\BaseLocationAction;
use Illuminate\Support\Facades\Http;

class FreeIPAPILocationAction extends BaseLocationAction
{
    protected string $apiBaseUrl = 'http://freeipapi.com/api/json/';

    protected array $response = [];

    public function __construct(
        public string $ip
    )
    {
        parent::__construct($ip); 

        $this->getLocation();
    }

    public function getLocation(): void
    {
        rescue(function() {
            $response = Http::retry(2, 1000)->get($this->apiBaseUrl . $this->ip);

            if($response->successful())
            {
                $this->response = $response->json();
            }
        });
    }

    public function getCountry(): string | null
    {
        return optional($this->response)['countryName'];
    }
}
