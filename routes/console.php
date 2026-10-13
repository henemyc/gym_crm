<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('gymxbook:about', function () {
    $this->info('GymXBook CRM — manage every gym lead with clarity.');
})->purpose('Display a short GymXBook CRM message');
