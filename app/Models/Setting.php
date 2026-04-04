<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) return $default;
        
        // Handle boolean strings
        if ($setting->value === '1' || $setting->value === 'true') return true;
        if ($setting->value === '0' || $setting->value === 'false') return false;
        
        return $setting->value;
    }

    public static function setValue($key, $value)
    {
        if (is_bool($value)) {
            $value = $value ? '1' : '0';
        }
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
