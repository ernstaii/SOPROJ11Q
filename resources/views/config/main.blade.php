@extends('layout')

@section('head')
    <link rel="stylesheet" href="{{asset('stylesheets/mainGameScreenStyle.css')}}">
    <link rel="stylesheet" href="{{asset('stylesheets/mapStyle.css')}}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
          integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
          crossorigin=""/>
    <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""></script>
    <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
    <script src="{{asset('scripts/keyGen.js')}}" defer></script>
    <script src="{{asset('scripts/resizeScript.js')}}" defer></script>
    <script src="{{asset('scripts/mapScript.js')}}" defer></script>
    <script src="{{asset('scripts/presetScript.js')}}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Configuratie Spel {{$id}}</title>
@endsection

@section('content')
    <div class="config-main-screen">
        <div class="box shadow">
            <div class="item-header">
                <h2>SPEL CONFIGURATIE</h2>
            </div>
            <div class="item-box">
                <div id="form_box">
                    <div class="config-form">
                        <form id="start_game_form" class="form-col-1" action="{{route('games.update', ['game' => $id])}}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-item">
                                <label class="form-label-0" for="duration">Speelduur (min)</label>
                                <input name="duration" class="input-numeric-0" id="duration" type="number" min="10" max="1440" value="120">
                            </div>
                            <div class="form-item">
                                <label class="form-label-0" for="interval">Locatieupdate interval (sec)</label>
                                <input name="interval" class="input-numeric-0" id="interval" type="number" min="30" max="300" value="30">
                            </div>
                            <div class="form-item">
                                <label class="form-label-0" for="colour">Kleurthema (app)</label>
                                <input name="colour" class="input-numeric-0" id="colour" type="color" value="#0099ff">
                            </div>
                            <div class="form-item" id="upload_box">
                                <label class="form-label-0" for="logo">Logo (app)</label>
                                <input name="logo" class="input-numeric-0" id="logo" type="file" accept="image/*" onchange="changeImageElement()">
                            </div>
                            <div class="form-item" id="img_element_box">
                            </div>
                            <div class="form-item">
                                <button type="input" class="keys-share-button" type="submit" name="state"
                                        value="{{\App\Enums\Statuses::Ongoing}}">Start spel
                                </button>
                            </div>
                        </form>
                        <div class="form-col-2">
                            <div class="form-item" id="code_input">
                                <label class="form-label-0" for="num_participants">Aantal spelers</label>
                                <input min="1" max="50" name="num_participants" class="input-numeric-0"
                                       id="participants_number" type="number" value="20">
                            </div>
                            <div class="form-item" id="code_button">
                                <button class="submit-button-0" id="send_number" onclick="generateKey({{$id}})">Genereer
                                    codes
                                </button>
                            </div>
                            <div class="form-item" id="ratio_slider">
                                <label class="form-label-0" for="ratio-slider">Ratio Agenten : Boeven</label>
                                <input name="ratio-slider" type="range" min="0" max="100" value="50" class="slider"
                                       id="ratio_range">
                                <div class="slider-labels">
                                    <p>0%</p>
                                    <p>50%</p>
                                    <p>100%</p>
                                </div>
                                <div class="slider-labels">
                                    <p>Minder agenten</p>
                                    <p>Meer agenten</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box shadow" id="outer_keys_box">
            <div class="item-header">
                <h2>CODES</h2>
            </div>
            <div class="item-box" id="keys_item_box">
                <div id="keys_box" class="keys-box">
                    <div class="key-item">
                        <h3><b>Politie</b></h3>
                    </div>
                    <div class="key-item">
                        <h3><b>Boeven</b></h3>
                    </div>
                    <div id="police_keys_box" class="vert-keys-box">
                        @foreach($police_keys as $key)
                            <div class="key-item">
                                <p id="somekey">{{ $key->value }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div id="thieves_keys_box" class="vert-keys-box">
                        @foreach($thief_keys as $key)
                            <div class="key-item">
                                <p id="somekey">{{ $key->value }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mouse-hover-event"></div>
            </div>
            <div id="keys_button_box">
                <button class="generic-button" id="copy_button_agents" onclick="performCopyAction('agent')">Kopieer politie codes</button>
                <button class="generic-button" id="copy_button_thiefs" onclick="performCopyAction('thief')">Kopieer boeven codes</button>
                <button class="generic-button" id="print_button" onclick="printKeys()">Print codes</button>
            </div>
        </div>
        <div class="total-map-box">
            <div class="map-top-tabs">
                <div class="map-top-tab" id="tab_1"><p>Spelgrenzen</p></div>
                <div class="map-top-tab" id="tab_2"><p>Buit</p></div>
                <div class="map-top-tab" id="tab_3"><p>Politiebureau</p></div>
            </div>
            <div class="mapbox shadow">
                <div id="map"></div>
                <button onclick="removeLastMarker()" id="button_remove_markers">Verwijder laatste marker</button>
                <button onclick="saveMarkers({{$id}})" id="button_save_markers" title="Er zijn minstens 3 markers nodig voordat het veld opgeslagen kan worden.">Sla speelveld op</button>
            </div>
        </div>
        <div class="bottom-box shadow">
            <div class="item-header">
                <h2>TEMPLATES</h2>
            </div>
            <div class="item-box">
                <div class="form-item game-form" id="preset-box">
                    <label for="presets">Selecteer een bestaande template</label>
                    <select id="presets" onchange="loadPreset({{$id}})">
                            <option value="-1" selected>Kies een template...</option>
                        @foreach($presets as $preset)
                            <option value="{{ $preset }}">{{ $preset->name }}</option>
                        @endforeach
                    </select>
                    <label for="preset_name">Maak een nieuwe template aan</label>
                    <input type="text" id="preset_name" name="name" value="{{ old('name') }}" placeholder="Vul hier de naam van het nieuwe template in...">
                    <button onclick="savePreset()" class="keys-share-button" id="save_preset_button">Opslaan</button>
                </div>
            </div>
        </div>
        <div class="bottom-box shadow">
            <div class="item-header">
                <h2>SPEL DETAILS</h2>
            </div>
            <div class="item-box" id="id_box">
                <p>Het hudige spel:</p>
                <h1>{{$name}}</h1>
                <h3>ID: {{$id}}</h3>
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            @foreach($presets as $preset)
                presets.push({
                    id: {{$preset->id}},
                    name: '{{$preset->name}}'
                });
            @endforeach
            @foreach($border_markers as $border_marker)
                applyExistingMarker({{$border_marker->location}});
            @endforeach
            drawLinesForExistingMarkers({{$id}});
            @foreach($loot as $loot_item)
                applyExistingLoot({{$loot_item->location}}, '{{$loot_item->name}}');
            @endforeach
            checkLootState({{$id}});
            @if (isset($police_station_location))
                applyExistingPoliceStation({{$police_station_location}});
            @endif
            applySidebarHrefs({{$name}});
        });
    </script>
@endsection

