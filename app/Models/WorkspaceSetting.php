<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkspaceSetting extends Model
{
    protected $fillable = ['setting_key', 'setting_value'];

    protected function casts(): array
    {
        return ['setting_value' => 'encrypted'];
    }

    public static function secret(string $key): ?string
    {
        $value = static::query()->where('setting_key', $key)->first()?->setting_value;

        return is_string($value) && $value !== '' ? $value : null;
    }

    public static function putSecret(string $key, string $value): self
    {
        $setting = static::query()->firstOrNew(['setting_key' => $key]);
        $setting->setting_value = $value;
        $setting->save();

        return $setting;
    }
}
