<?php

namespace App\Actions;

use Hidehalo\Nanoid\Client;

class GenerateShortId
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function execute(): string
    {
        return $this->client->generateId(size: config('lynx.short_id_size'));
    }
}
