<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadImport extends Model
{
    protected $fillable = [
        'user_id', 'filename', 'file_path', 'status', 'headers', 'mapping', 'summary',
        'rows_total', 'rows_valid', 'rows_invalid', 'rows_imported',
    ];

    protected function casts(): array
    {
        return ['headers' => 'array', 'mapping' => 'array', 'summary' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
