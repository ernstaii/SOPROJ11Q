<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePresetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'between:3,40', 'unique:game_presets,name'],
            'duration' => ['required', 'integer', 'between:10,1440'],
            'interval' => ['required', 'integer', 'between:30,300'],
            'police_station_lat' => ['required'],
            'police_station_lng' => ['required'],
            'loot_lats' => ['required', 'array', 'min:1'],
            'loot_lngs' => ['required', 'array', 'min:1'],
            'loot_names' => ['required', 'array'],
            'border_lats' => ['required', 'array', 'min:3'],
            'border_lngs' => ['required', 'array', 'min:3'],
            'colour_theme' => ['nullable', 'string'],
            'logo' => ['nullable', 'string']
        ];
    }
}
