<?php

namespace App\Http\Controllers;

use App\Enums\Statuses;
use App\Enums\UserStatuses;
use App\Events\GadgetAmountUpdatedEvent;
use App\Events\PlayerJoinedGameEvent;
use App\Events\ThiefCaughtEvent;
use App\Http\Requests\StoreGadgetRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Requests\UserStoreRequest;
use App\Models\Gadget;
use App\Models\InviteKey;
use App\Models\User;
use Carbon\Carbon;

class UserController extends Controller
{
    public function get(User $user)
    {
        $inviteKey = $user->inviteKey;

        if (isset($inviteKey)) {
            $user->role = $inviteKey->role;
        }

        return $user;
    }

    public function catchThief(User $user)
    {
        if ($user->status != UserStatuses::Playing) {
            response()->json(['errors' => [
                'value' => ['Alleen spelers die niet gevangen of in de lobby zijn kunnen gevangen worden.']
            ]], 422)->throwResponse();
        }

        $user->status = UserStatuses::Caught;
        $user->caught_at = Carbon::now();
        $user->save();
        event(new ThiefCaughtEvent($user));
    }

    public function store(UserStoreRequest $request)
    {
        $user = User::create([
            'username' => $request->username,
            'location' => $request->location,
        ]);

        $inviteKey = InviteKey::where('value', '=', $request->invite_key)->first();
        $inviteKey->user_id = $user->id;
        $inviteKey->save();

        if (in_array($inviteKey->game->status, [Statuses::Ongoing, Statuses::Paused])) {
            $user->status = UserStatuses::Playing;
            event(new PlayerJoinedGameEvent($inviteKey->game->id, $user));
        }

        $user->save();
        return $user;
    }

    public function update(UpdateLocationRequest $request, User $user)
    {
        $user->location = $request->location;
        $user->last_verified_at = Carbon::now();
        if($user->status != UserStatuses::Caught && $user->status != UserStatuses::Retired) {
            $user->status = UserStatuses::Playing;
        }
        $user->save();

        return $user;
    }

    public function updateGadget(User $user, Gadget $gadget)
    {
        $gadgetObj = $user->gadgets()->find($gadget->id)->pivot;
        if ($gadgetObj->amount > 0)
            $gadgetObj->amount -= 1;

        $gadgetObj->in_use = true;
        $gadgetObj->location = $user->location;
        $gadgetObj->activated_at = Carbon::now();

        $gadgetObj->update();
        return $user->gadgets()->get();
    }

    /**
     * AJAX function. Not to be called via manual routing.
     *
     * @param StoreGadgetRequest $request
     * @param User $user
     * @return bool
     */
    public function manageGadget(StoreGadgetRequest $request, User $user)
    {
        $gadgets = $user->gadgets()->get();
        foreach ($gadgets as $gadget) {
            if ($gadget->name === $request->gadget_name) {
                if ($request->operator === 'add')
                    $gadget->pivot->amount += 1;
                else {
                    if ($gadget->pivot->amount === 1) {
                        $user->gadgets()->detach(Gadget::whereName($request->gadget_name)->first()->id);
                        event(new GadgetAmountUpdatedEvent($user->get_game()->id, $user));
                        return true;
                    }
                    $gadget->pivot->amount -= 1;
                }
                $gadget->pivot->update();
                event(new GadgetAmountUpdatedEvent($user->get_game()->id, $user));
                return true;
            }
        }

        if ($request->operator === 'add') {
            $user->gadgets()->attach(Gadget::whereName($request->gadget_name)->first()->id, array('amount' => 1));
            event(new GadgetAmountUpdatedEvent($user->get_game()->id, $user));
            return true;
        }
        return false;
    }

    public function triggerAlarm(User $user)
    {
        $user->triggered_alarm = true;
        $user->save();
    }
}
