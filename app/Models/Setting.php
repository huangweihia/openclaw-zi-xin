<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * 获取设置（按 type 做基本反序列化）
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $row = static::query()->where('key', $key)->first();
        if (! $row) {
            return $default;
        }

        $type = $row->type;
        $value = $row->value;

        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default,
            'number' => is_numeric($value) ? (int) $value : $default,
            'json' => filled($value) ? json_decode($value, true) : $default,
            default => $value,
        };
    }

    public static function setValue(string $key, mixed $value, string $type = 'string', ?string $description = null): void
    {
        $storeValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'number' => (string) ((int) $value),
            'json' => json_encode($value, JSON_UNESCAPED_UNICODE),
            default => (string) $value,
        };

        static::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $storeValue,
                'type' => $type,
                'description' => $description,
            ]
        );
    }
}

