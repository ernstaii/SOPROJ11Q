@extends('layout')

@section('head')
    <link rel="stylesheet" href="{{asset('stylesheets/mainGameScreenStyle.css')}}">
    <link rel="stylesheet" href="{{asset('stylesheets/mapStyle.css')}}">
    <link rel="stylesheet" href="{{asset('stylesheets/gameOverviewStyle.css')}}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
          integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
          crossorigin=""/>
    <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""></script>
    <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
    <script src="{{asset('scripts/mapScript_gameOverview.js')}}" defer></script>
    <script src="{{asset('scripts/specialRolesScript.js')}}" defer></script>
    <script src="{{asset('scripts/gadgetScript.js')}}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Overzicht Spel {{$id}}</title>
@endsection

@section('content')
    <div class="game-main-screen">
        <div class="box shadow">
            <div class="item-header">
                <h2>SPEL STATUS BEHEREN</h2>
            </div>
            <div class="button-1">
                <form id="form_1" action="{{route('games.update', ['game' => $id])}}" method="post">
                    <div class="form-item game-form">
                        @csrf
                        @method('PUT')
                        <label for="reason">Geef een reden voor het beëindigen</label>
                        <div class="tooltip">
                            <span class="tooltiptext-bottom"><b class="big-question-mark">?</b>Vul hier de reden voor het beëindigen van het spel in.</span>
                            <input type="text" id="reason" name="reason">
                        </div>
                        <button type="input" class="keys-share-button" type="submit" name="state"
                                value="{{\App\Enums\Statuses::Finished}}">Beëindig spel
                        </button>
                    </div>
                </form>
            </div>
            <div class="button-2">
                <form id="form_2" action="{{route('games.update', ['game' => $id])}}" method="post">
                    <div class="form-item game-form">
                        @csrf
                        @method('PUT')
                        <label for="reason">Geef een reden voor het pauzeren</label>
                        <div class="tooltip">
                            <span class="tooltiptext-bottom"><b class="big-question-mark">?</b>Vul hier de reden voor het pauzeren van het spel in.</span>
                            <input type="text" id="reason" name="reason">
                        </div>
                        <button type="input" class="keys-share-button" type="submit" name="state"
                                value="{{\App\Enums\Statuses::Paused}}">Pauzeer spel
                        </button>
                    </div>
                </form>
            </div>
            <div class="button-3">
                <form id="form_3" action="{{route('games.update', ['game' => $id])}}" method="post">
                    <div class="form-item game-form">
                        @csrf
                        @method('PUT')
                        <button type="input" class="keys-share-button" type="submit" name="state"
                                value="{{\App\Enums\Statuses::Ongoing}}">Hervat spel
                        </button>
                    </div>
                </form>
            </div>
            <div class="button-4">
                <form id="form_4" action="{{route('games.sendMessage', ['game' => $id])}}" method="post">
                    <div class="form-item game-form">
                        @csrf
                        <label for="message">Stuur een bericht naar de spelers</label>
                        <div class="tooltip">
                            <span class="tooltiptext-bottom"><b class="big-question-mark">?</b>Vul hier het bericht in dat naar de spelers gestuurd moet worden.</span>
                            <input type="text" id="message" name="message">
                        </div>
                        <button type="input" class="keys-share-button" type="submit" name="state">Stuur bericht</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="mapbox shadow">
            <div id="map">
            </div>
            <div class="tooltip">
                <span class="tooltiptext"><b class="big-question-mark">?</b>Selecteer een buit door op een buit pin op de kaart te klikken. Klik vervolgens op deze knop om de buit uit het spel te verwijderen.</span>
                <button id="remove_loot_button" onclick="deletePrompt(selectedLootId)" disabled>Selecteer a.u.b. een buit</button>
            </div>
            <div class="tooltip">
                <span class="tooltiptext"><b class="big-question-mark">?</b>Vul hier de naam van de nieuwe buit in. Plaats de buit vervolgens door op de kaart te klikken.</span>
                <input type="text" id="loot_name_input" placeholder="Voer hier de buit naam in...">
            </div>
        </div>
        <div class="timer-box shadow">
            <div class="center-box">
                <h2>Totale speelduur: {{$duration}} minuten</h2>
                <div class="timer-with-label">
                    @if($game_status != 'finished')
                        <h1 class="timer">00:00:00</h1>
                        <h2 class="score-text">{{$status_text}}</h2>
                    @else
                        <h1 class="timer">{{$status_text}}</h1>
                    @endif
                </div>
                <p class="score-text" id="score_1">Boeven score: {{$thieves_score}}</p>
                <p class="score-text" id="score_2">Politie score: {{$police_score}}</p>
            </div>
        </div>
        <div class="bottom-box shadow">
            <div class="item-header">
                <h2>SPEL NOTIFICATIES</h2>
            </div>
            <div class="messages">
            </div>
        </div>
        <div class="bottom-box shadow">
            <div class="item-header">
                <h2>SPEL DETAILS</h2>
            </div>
            <div class="item-box" id="id_box">
                <p>Het huidige spel:</p>
                <h1>{{$name}}</h1>
                <h3>ID: {{$id}}</h3>
            </div>
        </div>
        <div class="bottom-box shadow">
            <div class="item-header">
                <h2>SPECIALE ROLLEN</h2>
            </div>
            <div class="item-box" id="special_roles_box">
                @foreach($users as $user)
                    @if($user->role === \App\Enums\Roles::Thief)
                        <div class="thief-spec-role-box" id="thief_box_{{$user->id}}">
                            <p class="thief-spec-role-name" id="thief_name_{{$user->id}}">{{$user->username}}</p>
                            <div class="thief-spec-role-checkbox-box">
                                <label for="is_fake_agent" class="thief-spec-role-checkbox-text">Nep agent?</label>
                                @if ($user->is_fake_agent)
                                    <input name="is_fake_agent" type="checkbox" class="thief-spec-role-checkbox" id="thief_fake_agent_checkbox_{{$user->id}}" onchange="setSpecialRole({{$user->id}}, {{$id}})" checked>
                                @else
                                    <input name="is_fake_agent" type="checkbox" class="thief-spec-role-checkbox" id="thief_fake_agent_checkbox_{{$user->id}}" onchange="setSpecialRole({{$user->id}}, {{$id}})">
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="bottom-box shadow" id="gadget_box_background">
            <div class="item-header">
                <h2>GADGETS</h2>
            </div>
            <div class="item-box" id="gadget_box">
                <button class="gadgets-add-all-button" onclick="addAllGadgets({{$id}})">Geef iedereen één gadget</button>
                <div class="gadget-total-box">
                    <div class="left-column">
                        <h3 class="player-role-header">Politie</h3>
                        @foreach($users as $user)
                            @if($user->role === \App\Enums\Roles::Police)
                                <div class="user-box">
                                    <span class="user-box-name" id="user_{{$user->id}}">{{$user->username}}</span>
                                    <div class="user-box-buttons-box">
                                        <div class="user-box-buttons-divider">
                                            <label id="alarm">Alarm</label>
                                            @if($user->gadgets()->whereName(\App\Enums\Gadgets::Alarm)->first() !== null)
                                                <label id="amount_of_alarms_{{$user->id}}">{{$user->gadgets()->whereName(\App\Enums\Gadgets::Alarm)->first()->pivot->amount}}</label>
                                            @else
                                                <label id="amount_of_alarms_{{$user->id}}">0</label>
                                            @endif
                                                <a class="user-box-button add-button" id="add_alarm_button" onclick="manageGadget('alarm', 'add', {{$user->id}})">+</a>
                                                <a class="user-box-button remove-button" id="remove_alarm_button" onclick="manageGadget('alarm', 'remove', {{$user->id}})">─</a>
                                        </div>
                                        <div class="user-box-buttons-divider">
                                            <label id="drone">Drone</label>
                                            @if($user->gadgets()->whereName(\App\Enums\Gadgets::Drone)->first() !== null)
                                                <label id="amount_of_drones_{{$user->id}}">{{$user->gadgets()->whereName(\App\Enums\Gadgets::Drone)->first()->pivot->amount}}</label>
                                            @else
                                                <label id="amount_of_drones_{{$user->id}}">0</label>
                                            @endif
                                            <a class="user-box-button add-button" id="add_drone_button" onclick="manageGadget('drone', 'add', {{$user->id}})">+</a>
                                            <a class="user-box-button remove-button" id="remove_drone_button" onclick="manageGadget('drone', 'remove', {{$user->id}})">─</a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="right-column">
                        <h3 class="player-role-header">Boeven</h3>
                        @foreach($users as $user)
                            @if($user->role === \App\Enums\Roles::Thief)
                                <div class="user-box">
                                    <span class="user-box-name" id="user_{{$user->id}}">{{$user->username}}</span>
                                    <div class="user-box-buttons-box">
                                        <div class="user-box-buttons-divider">
                                            <label id="smokescreen">Rookgordijn</label>
                                            @if($user->gadgets()->whereName(\App\Enums\Gadgets::Smokescreen)->first() !== null)
                                                <label id="amount_of_smokescreens_{{$user->id}}">{{$user->gadgets()->whereName(\App\Enums\Gadgets::Smokescreen)->first()->pivot->amount}}</label>
                                            @else
                                                <label id="amount_of_smokescreens_{{$user->id}}">0</label>
                                            @endif
                                            <a class="user-box-button add-button" id="add_smokescreen_button" onclick="manageGadget('smokescreen', 'add', {{$user->id}})">+</a>
                                            <a class="user-box-button remove-button" id="remove_smokescreen_button" onclick="manageGadget('smokescreen', 'remove', {{$user->id}})">─</a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            setGameDetails({{$id}}, '{{$name}}');
            @foreach($loot as $loot_item)
            applyLootMarker({{$loot_item->location}}, '{{$loot_item->name}}', '{{$loot_item->id}}');
            @endforeach
            @foreach($users as $user)
            @if ($user->location != null)
            applyUserMarker({{$user->location}}, '{{$user->username}}', '{{$user->role}}');
            @endif
            @endforeach
            updateUserPinsOnChange({{$interval}}, '{{$game_status}}', {{$id}});
            @foreach($border_markers as $border_marker)
            applyExistingMarker({{$border_marker->location}});
            @endforeach
            drawLinesForExistingMarkers();
            @if($game_status != 'finished')
            handleTimerElement('{{$game_status}}', '{{$time_left}}', '{{$duration}}');
            @endif
            applyExistingPoliceStation({{$police_station_location}});
            callGameDetails({{$id}});
            getAllUserIds({{json_encode($userIds, JSON_HEX_TAG)}});
        });
    </script>
@endsection
