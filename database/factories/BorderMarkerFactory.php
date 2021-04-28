<?php

namespace Database\Factories;

use App\Models\BorderMarker;
use Illuminate\Database\Eloquent\Factories\Factory;

class BorderMarkerFactory extends Factory
{
    protected $model = BorderMarker::class;

    public function definition()
    {
        $latitude = "51.7" . strval(rand(30421, 34703));
        $longitude = "5." . strval(rand(470841, 482090));

        return [
            'location' => $latitude . ',' . $longitude
        ];
    }

    public function isFirstMarker()
    {
        return $this->state(function (array $attributes) {
            return [
                'location' => "51.7" . strval(rand(30421, 34703)) . ",5." . strval(rand(470841, 482090))
            ];
        });
    }

    public function isSecondMarker()
    {
        return $this->state(function (array $attributes) {
            return [
                'location' => "51.7" . strval(rand(31104, 35002)) . ",5." . strval(rand(593363, 598034))
            ];
        });
    }

    public function isThirdMarker()
    {
        return $this->state(function (array $attributes) {
            return [
                'location' => "51.7" . strval(rand(90136, 93482)) . ",5." . strval(rand(576329, 579863))
            ];
        });
    }

    public function isFourthMarker()
    {
        return $this->state(function (array $attributes) {
            return [
                'location' => "51.7" . strval(rand(76128, 79084)) . ",5." . strval(rand(528871, 543762))
            ];
        });
    }

    public function isFifthMarker()
    {
        return $this->state(function (array $attributes) {
            return [
                'location' => "51.7" . strval(rand(80532, 82789)) . ",5." . strval(rand(474034, 497632))
            ];
        });
    }
}
