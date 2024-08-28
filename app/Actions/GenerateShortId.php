<?php

namespace App\Actions;

use Hidehalo\Nanoid\Client;
use Hidehalo\Nanoid\CoreInterface;

class GenerateShortId
{
    private Client $client;

    private int $size;

    private string $characters;

    public function __construct()
    {
        $this->client = new Client();
        $this->size = config('lynx.short_id_size', 8);
        $this->characters = config('lynx.allowed_characters', CoreInterface::SAFE_SYMBOLS);
    }

    public function execute(): string
    {
        return $this->client->formattedId(
            alphabet: $this->characters, size: $this->size
        );
    }
}
