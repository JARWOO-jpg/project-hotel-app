<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'group', 'label', 'type', 'value'];

    /**
     * Ambil nilai setting berdasarkan key
     */
    public static function getValue(string $key, $default = null): ?string
    {
        $setting = static::where('key', $key)->first();
        return $setting?->value ?? $default;
    }

    /**
     * Set nilai setting
     */
    public static function setValue(string $key, ?string $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
    }

    /**
     * Ambil semua setting dalam satu grup
     */
    public static function getGroup(string $group): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('group', $group)->get();
    }
}
