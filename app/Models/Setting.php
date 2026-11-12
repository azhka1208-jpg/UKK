<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
    ];

    public function typedValue(): mixed
    {
        return match ($this->type) {
            'integer' => (int) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'decimal' => (float) $this->value,
            default => $this->value,
        };
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->typedValue() : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = is_bool($value) ? ($value ? '1' : '0') : (string) $value;
        $setting->save();
    }
}