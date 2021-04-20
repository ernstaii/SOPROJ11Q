<?php

namespace App\Http\Requests;

use App\Enums\Statuses;
use App\Models\Game;
use App\Rules\IsInStateRule;
use Composer\XdebugHandler\Status;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;
use OutOfBoundsException;

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
                'status' => 'Het spel mag niet omgezet worden naar ' . $this->state . ' vanuit ' . $this->game->status . '.'
            ]);

        if (!$this->game->hasKeys())
            throw ValidationException::withMessages([
                'hasKeys' => 'Het spel heeft nog geen invite keys.'
            ]);

        if ($this->game->status === Statuses::Config) {
            return [
                'state' => ['required', 'string'],
                'duration' => ['required', 'integer', 'between:10,1440'],
                'interval' => ['required', 'integer', 'between:30,300']
            ];
        }
        return [
            'state' => ['required', 'string']
        ];
    }
}
