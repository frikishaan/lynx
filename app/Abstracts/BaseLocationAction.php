<?php

namespace App\Abstracts;

abstract class BaseLocationAction
{
    public function __construct(
        public string $ip
    )
    {
        //
    }

    abstract public function getCountry();

    // public function getContinent(): string;

    // public function getCity(): string;
}