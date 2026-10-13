<?php

use Carbon\CarbonInterface;
use Illuminate\Support\Str;

if (! function_exists('crm_label')) {
    function crm_label(?string $value): string
    {
        if (! $value) {
            return '—';
        }

        return Str::of($value)->replace('_', ' ')->title()->toString();
    }
}

if (! function_exists('crm_relative_date')) {
    function crm_relative_date(?CarbonInterface $date): string
    {
        return $date?->diffForHumans() ?? 'Not scheduled';
    }
}
