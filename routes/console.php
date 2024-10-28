<?php

use App\Jobs\DeleteExpiredLinks;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new DeleteExpiredLinks)->dailyAt('01:00');
