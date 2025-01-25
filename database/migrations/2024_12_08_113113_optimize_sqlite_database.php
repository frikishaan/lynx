<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if(DB::getDriverName() === 'sqlite' && app()->environment('production'))
        {
            DB::statement('PRAGMA journal_mode = WAL;');
            DB::statement('PRAGMA synchronous = NORMAL;');
            DB::statement('PRAGMA page_size = 8192;');
            DB::statement('PRAGMA cache_size = -20000;');
            DB::statement('PRAGMA auto_vacuum = incremental;');
        }
    }
};
