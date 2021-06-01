<?php

namespace App\Http\Requests;

use App\Enums\Statuses;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGameStateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $accepted_states = array();
        switch ($this->state) {
            case Statuses::Ongoing:
                $accepted_states = [Statuses::Config, Statuses::Paused];
                break;
            case Statuses::Paused:
                $accepted_states = [Statuses::Ongoing];
                break;
            case Statuses::Finished:
                $accepted_states = [Statuses::Ongoing, Statuses::Paused];
                break;
            default:
                throw ValidationException::withMessages([
                    'status' => 'De opgegeven status is niet geldig.'
                ]);
        }

        if (!in_array($this->game->status, $accepted_states))
            throw ValidationException::withMessages([
                'status' => 'Het spel mag niet omgezet worden naar ' . $this->getTranslatedState($this->state) . ' vanuit ' . $this->getTranslatedState($this->game->status) . '.'
            ]);

        if (!$this->game->has_keys())
            throw ValidationException::withMessages([
                'hasKeys' => 'Het spel heeft nog geen toegangscodes.'
            ]);

        if ($this->game->status === Statuses::Config) {
            if ($this->game->border_markers()->count() < 3)
                throw ValidationException::withMessages([
                    'hasBorderMarkers' => 'Creëer a.u.b. eerst een speelveld door minimaal drie grens pinnen op de kaart te plaatsen en dan op de knop "Sla speelveld op" te klikken.'
                ]);
            if ($this->game->loot()->count() < 1)
                throw ValidationException::withMessages([
                    'hasLoot' => 'Voeg a.u.b. eerst buit toe aan het spel door op de kaart te klikken en dan op de knop "Sla buit op" te klikken.'
                ]);
            if (!isset($this->game->police_station_location))
                throw ValidationException::withMessages([
                    'hasPoliceStationLocation' => 'Voeg a.u.b. eerst een politiebureau toe aan het spel door op de kaart te klikken en dan op de knop "Sla politiebureau op" te klikken.'
                ]);
            return [
                'state' => ['required', 'string'],
                'duration' => ['required', 'integer', 'between:10,1440'],
                'interval' => ['required', 'integer', 'between:30,300'],
                'logo' => ['nullable', 'image', 'dimensions:max_width=300,max_height=200'],
                'colour' => ['nullable', 'string']
            ];
        }
        return [
            'state' => ['required', 'string']
        ];
    }

    public function messages()
    {
        return [
            'state.required' => 'Vul a.u.b. een spel status in.',
            'state.string' => 'Vul a.u.b. een spel status in van het type "string".',
            'duration.required' => 'Vul a.u.b. een speelduur in.',
            'duration.integer' => 'Vul a.u.b. een getal in voor de speelduur.',
            'duration.between' => 'Vul a.u.b. een speelduur in van minimaal 10 minuten en maximaal 1440 minuten.',
            'interval.required' => 'Vul a.u.b. een interval voor locatieupdates in.',
            'interval.integer' => 'Vul a.u.b. een getal in voor het interval voor locatieupdates.',
            'interval.between' => 'Vul a.u.b. een interval voor locatieupdates in van minimaal 30 seconden en maximaal 300 seconden.',
            'logo.nullable' => 'Ongeldige waarde opgegeven voor het logo.',
            'logo.image' => 'Upload a.u.b. een logo van het type "image".',
            'logo.dimensions' => 'Upload a.u.b. een afbeelding welke maximaal 300x200 pixels groot is.',
            'colour.nullable' => 'Ongeldige waarde opgegeven voor het kleurthema.',
            'colour.string' => 'Vul a.u.b. een kleurthema in van het type "string".'
        ];
    }

    private function getTranslatedState(string $state)
    {
        switch ($state)
        {
            case Statuses::Config:
                return '"Configuratie"';
            case Statuses::Ongoing:
                return '"Gaande"';
            case Statuses::Paused:
                return '"Gepauzeerd"';
            case Statuses::Finished:
                return '"Beëindigd"';
            default:
                return '';
        }
    }
}
