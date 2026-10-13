<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GooglePlacesApiUsage extends Model
{
    protected $table = 'google_places_api_usage';

    protected $fillable = ['billing_month', 'sku', 'reserved_requests'];

    protected function casts(): array
    {
        return ['reserved_requests' => 'integer'];
    }
}
