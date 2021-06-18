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
            'name' => ['required', 'string', 'between:3,40'],
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

    public function messages()
    {
        return [
            'name.required' => 'Vul a.u.b. een naam in voor het template.',
            'name.string' => 'Vul a.u.b. een naam in voor het template van het type "string".',
            'name.between' => 'Vul a.u.b. een naam in voor het template met een minimum lengte van 3 tekens en een maximum lengte van 40 tekens.',
            'duration.required' => 'Vul a.u.b. een speelduur in.',
            'duration.integer' => 'Vul a.u.b. een getal in voor de speelduur.',
            'duration.between' => 'Vul a.u.b. een speelduur in van minimaal 10 minuten en maximaal 1440 minuten.',
            'interval.required' => 'Vul a.u.b. een interval voor locatieupdates in.',
            'interval.integer' => 'Vul a.u.b. een getal in voor het interval voor locatieupdates.',
            'interval.between' => 'Vul a.u.b. een interval voor locatieupdates in van minimaal 30 seconden en maximaal 300 seconden.',
            'police_station_lat.required' => 'Plaats a.u.b. een politiebureau op de kaart. (Er mist een breedtegraad!)',
            'police_station_lng.required' => 'Plaats a.u.b. een politiebureau op de kaart. (Er mist een lengtegraad!)',
            'loot_lats.required' => 'Zorg er a.u.b. voor dat er een lijst aan breedtegraden wordt opgestuurd. (Buit)',
            'loot_lats.array' => 'Zorg er a.u.b. voor dat er een lijst aan breedtegraden wordt opgestuurd. (Buit)',
            'loot_lats.min' => 'Zorg er a.u.b. voor dat er minimaal één breedtegraad aanwezig is. (Buit)',
            'loot_lngs.required' => 'Zorg er a.u.b. voor dat er een lijst aan lengtegraden wordt opgestuurd. (Buit)',
            'loot_lngs.array' => 'Zorg er a.u.b. voor dat er een lijst aan lengtegraden wordt opgestuurd. (Buit)',
            'loot_lngs.min' => 'Zorg er a.u.b. voor dat er minimaal één lengtegraad aanwezig is. (Buit)',
            'loot_names.required' => 'Zorg er a.u.b. voor dat er een lijst aan namen wordt opgestuurd. (Buit)',
            'loot_names.array' => 'Zorg er a.u.b. voor dat er een lijst aan namen wordt opgestuurd. (Buit)',
            'border_lats.required' => 'Zorg er a.u.b. voor dat er een lijst aan breedtegraden wordt opgestuurd. (Grens-pin)',
            'border_lats.array' => 'Zorg er a.u.b. voor dat er een lijst aan breedtegraden wordt opgestuurd. (Grens-pin)',
            'border_lats.min' => 'Zorg er a.u.b. voor dat er minimaal drie breedtegraden aanwezig zijn. (Grens-pin)',
            'border_lngs.required' => 'Zorg er a.u.b. voor dat er een lijst aan lengtegraden wordt opgestuurd. (Grens-pin)',
            'border_lngs.array' => 'Zorg er a.u.b. voor dat er een lijst aan lengtegraden wordt opgestuurd. (Grens-pin)',
            'border_lngs.min' => 'Zorg er a.u.b. voor dat er minimaal drie lengtegraden aanwezig zijn. (Grens-pin)',
            'colour_theme.nullable' => 'Ongeldige waarde opgegeven voor het kleurthema.',
            'colour_theme.string' => 'Vul a.u.b. een kleurthema in van het type "string".',
            'logo.nullable' => 'Ongeldige waarde opgegeven voor het logo.',
            'logo.string' => 'Vul a.u.b. een logo in van het type "string".'
        ];
    }
}
