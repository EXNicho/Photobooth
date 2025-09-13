<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class ShowSettings extends Command
{
    protected $signature = 'debug:settings';
    protected $description = 'Show settings table contents';

    public function handle(): int
    {
        $rows = Setting::query()->orderBy('key')->get(['key','value']);
        foreach ($rows as $row) {
            $this->line($row->key.' = '.$row->value);
        }
        return self::SUCCESS;
    }
}

