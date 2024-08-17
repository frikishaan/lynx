<?php

namespace App\Actions;

use DOMDocument;
use Exception;

class GetTitleFromURL
{
    public function execute(string $url): string
    {
        try {

            $html = file_get_contents($url);
    
            if ($html === false) {
                return '';
            }
    
            $doc = new DOMDocument();

            // Suppress errors due to malformed HTML
            @$doc->loadHTML($html);
    
            // Get the title element
            $title = $doc->getElementsByTagName('title')->item(0);

            return $title ? trim($title->textContent) : '';
        }
        catch(Exception $ex) {
            return '';
        }
    }
}
