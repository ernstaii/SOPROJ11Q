<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\BorderMarker;
use App\Models\Game;
use App\Models\InviteKey;
use App\Models\Loot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class RandomGameSeeder extends Seeder
{
    const USERNAMES = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Miller', 'Davis', 'Garcia', 'Rodriguez', 'Wilson', 'Martinez', 'Anderson', 'Taylor', 'Thomas', 'Hernandez', 'Moore', 'Martin', 'Jackson', 'Thompson', 'White', 'Lopez', 'Lee', 'Gonzalez', 'Harris', 'Clark', 'Lewis', 'Robinson', 'Walker', 'Perez', 'Hall', 'Young', 'Allen', 'Sanchez', 'Wright', 'King', 'Scott', 'Green', 'Baker', 'Adams', 'Nelson', 'Hill', 'Ramirez', 'Campbell', 'Mitchell', 'Roberts', 'Carter', 'Phillips', 'Evans', 'Turner', 'Torres', 'Parker', 'Collins', 'Edwards', 'Stewart', 'Flores', 'Morris', 'Nguyen', 'Murphy', 'Rivera', 'Cook', 'Rogers', 'Morgan', 'Peterson', 'Cooper', 'Reed', 'Bailey', 'Bell', 'Gomez', 'Kelly', 'Howard', 'Ward', 'Cox', 'Diaz', 'Richardson', 'Wood', 'Watson', 'Brooks', 'Bennett', 'Gray', 'James', 'Reyes', 'Cruz', 'Hughes', 'Price', 'Myers', 'Long', 'Foster', 'Sanders', 'Ross', 'Morales', 'Powell', 'Sullivan', 'Russell', 'Ortiz', 'Jenkins', 'Gutierrez', 'Perry', 'Butler', 'Barnes', 'Fisher', 'Henderson', 'Coleman', 'Simmons', 'Patterson', 'Jordan', 'Reynolds', 'Hamilton', 'Graham', 'Kim', 'Gonzales', 'Alexander', 'Ramos', 'Wallace', 'Griffin', 'West', 'Cole', 'Hayes', 'Chavez', 'Gibson', 'Bryant', 'Ellis', 'Stevens', 'Murray', 'Ford', 'Marshall', 'Owens', 'Mcdonald', 'Harrison', 'Ruiz', 'Kennedy', 'Wells', 'Alvarez', 'Woods', 'Mendoza', 'Castillo', 'Olson', 'Webb', 'Washington', 'Tucker', 'Freeman', 'Burns', 'Henry', 'Vasquez', 'Snyder', 'Simpson', 'Crawford', 'Jimenez', 'Porter', 'Mason', 'Shaw', 'Gordon', 'Wagner', 'Hunter', 'Romero', 'Hicks', 'Dixon', 'Hunt', 'Palmer', 'Robertson', 'Black', 'Holmes', 'Stone', 'Meyer', 'Boyd', 'Mills', 'Warren', 'Fox', 'Rose', 'Rice', 'Moreno', 'Schmidt', 'Patel', 'Ferguson', 'Nichols', 'Herrera', 'Medina', 'Ryan', 'Fernandez', 'Weaver', 'Daniels', 'Stephens', 'Gardner', 'Payne', 'Kelley', 'Dunn', 'Pierce', 'Arnold', 'Tran', 'Spencer', 'Peters', 'Hawkins', 'Grant', 'Hansen', 'Castro', 'Hoffman', 'Hart', 'Elliott', 'Cunningham', 'Knight', 'Bradley', 'Carroll', 'Hudson', 'Duncan', 'Armstrong', 'Berry', 'Andrews', 'Johnston', 'Ray', 'Lane', 'Riley', 'Carpenter', 'Perkins', 'Aguilar', 'Silva', 'Richards', 'Willis', 'Matthews', 'Chapman', 'Lawrence', 'Garza', 'Vargas', 'Watkins', 'Wheeler', 'Larson', 'Carlson', 'Harper', 'George', 'Greene', 'Burke', 'Guzman', 'Morrison', 'Munoz', 'Jacobs', 'Obrien', 'Lawson', 'Franklin', 'Lynch', 'Bishop', 'Carr', 'Salazar', 'Austin', 'Mendez', 'Gilbert', 'Jensen', 'Williamson', 'Montgomery', 'Harvey', 'Oliver', 'Howell', 'Dean', 'Hanson', 'Weber', 'Garrett', 'Sims', 'Burton', 'Fuller', 'Soto', 'Mccoy', 'Welch', 'Chen', 'Schultz', 'Walters', 'Reid', 'Fields', 'Walsh', 'Little', 'Fowler', 'Bowman', 'Davidson', 'May', 'Day', 'Schneider', 'Newman', 'Brewer', 'Lucas', 'Holland', 'Wong', 'Banks', 'Santos', 'Curtis', 'Pearson', 'Delgado', 'Valdez', 'Pena', 'Rios', 'Douglas', 'Sandoval', 'Barrett', 'Hopkins', 'Keller', 'Guerrero', 'Stanley', 'Bates', 'Alvarado', 'Beck', 'Ortega', 'Wade', 'Estrada', 'Contreras', 'Barnett', 'Caldwell', 'Santiago', 'Lambert', 'Powers', 'Chambers', 'Nunez', 'Craig', 'Leonard', 'Lowe', 'Rhodes', 'Byrd', 'Gregory', 'Shelton', 'Frazier', 'Becker', 'Maldonado', 'Fleming', 'Vega', 'Sutton', 'Cohen', 'Jennings', 'Parks', 'Mcdaniel', 'Watts', 'Barker', 'Norris', 'Vaughn', 'Vazquez', 'Holt', 'Schwartz', 'Steele', 'Benson', 'Neal', 'Dominguez', 'Horton', 'Terry', 'Wolfe', 'Hale', 'Lyons', 'Graves', 'Haynes', 'Miles', 'Park', 'Warner', 'Padilla', 'Bush', 'Thornton', 'Mccarthy', 'Mann', 'Zimmerman', 'Erickson', 'Fletcher', 'Mckinney', 'Page', 'Dawson', 'Joseph', 'Marquez', 'Reeves', 'Klein', 'Espinoza', 'Baldwin', 'Moran', 'Love', 'Robbins', 'Higgins', 'Ball', 'Cortez', 'Le', 'Griffith', 'Bowen', 'Sharp', 'Cummings', 'Ramsey', 'Hardy', 'Swanson', 'Barber', 'Acosta', 'Luna', 'Chandler', 'Blair', 'Daniel', 'Cross', 'Simon', 'Dennis', 'Oconnor', 'Quinn', 'Gross', 'Navarro', 'Moss', 'Fitzgerald', 'Doyle', 'Mclaughlin', 'Rojas', 'Rodgers', 'Stevenson', 'Singh', 'Yang', 'Figueroa', 'Harmon', 'Newton', 'Paul', 'Manning', 'Garner', 'Mcgee', 'Reese', 'Francis', 'Burgess', 'Adkins', 'Goodman', 'Curry', 'Brady', 'Christensen', 'Potter', 'Walton', 'Goodwin', 'Mullins', 'Molina', 'Webster', 'Fischer', 'Campos', 'Avila', 'Sherman', 'Todd', 'Chang', 'Blake', 'Malone', 'Wolf', 'Hodges', 'Juarez', 'Gill', 'Farmer', 'Hines', 'Gallagher', 'Duran', 'Hubbard', 'Cannon', 'Miranda', 'Wang', 'Saunders', 'Tate', 'Mack', 'Hammond', 'Carrillo', 'Townsend', 'Wise', 'Ingram', 'Barton', 'Mejia', 'Ayala', 'Schroeder', 'Hampton', 'Rowe', 'Parsons', 'Frank', 'Waters', 'Strickland', 'Osborne', 'Maxwell', 'Chan', 'Deleon', 'Norman', 'Harrington', 'Casey', 'Patton', 'Logan', 'Bowers', 'Mueller', 'Glover', 'Floyd', 'Hartman', 'Buchanan', 'Cobb', 'French', 'Kramer', 'Mccormick', 'Clarke', 'Tyler', 'Gibbs', 'Moody', 'Conner', 'Sparks', 'Mcguire', 'Leon', 'Bauer', 'Norton', 'Pope', 'Flynn', 'Hogan', 'Robles', 'Salinas', 'Yates', 'Lindsey', 'Lloyd', 'Marsh', 'Mcbride', 'Owen', 'Solis', 'Pham', 'Lang', 'Pratt', 'Lara', 'Brock', 'Ballard', 'Trujillo', 'Shaffer', 'Drake', 'Roman', 'Aguirre', 'Morton', 'Stokes', 'Lamb', 'Pacheco', 'Patrick', 'Cochran', 'Shepherd', 'Cain', 'Burnett', 'Hess', 'Li', 'Cervantes', 'Olsen', 'Briggs', 'Ochoa', 'Cabrera', 'Velasquez', 'Montoya', 'Roth', 'Meyers', 'Cardenas', 'Fuentes', 'Weiss', 'Wilkins', 'Hoover', 'Nicholson', 'Underwood', 'Short', 'Carson', 'Morrow', 'Colon', 'Holloway', 'Summers', 'Bryan', 'Petersen', 'Mckenzie', 'Serrano', 'Wilcox', 'Carey', 'Clayton', 'Poole', 'Calderon', 'Gallegos', 'Greer', 'Rivas', 'Guerra', 'Decker', 'Collier', 'Wall', 'Whitaker', 'Bass', 'Flowers', 'Davenport', 'Conley', 'Houston', 'Huff', 'Copeland', 'Hood', 'Monroe', 'Massey', 'Roberson', 'Combs', 'Franco', 'Larsen', 'Pittman', 'Randall', 'Skinner', 'Wilkinson', 'Kirby', 'Cameron', 'Bridges', 'Anthony', 'Richard', 'Kirk', 'Bruce', 'Singleton', 'Mathis', 'Bradford', 'Boone', 'Abbott', 'Charles', 'Allison', 'Sweeney', 'Atkinson', 'Horn', 'Jefferson', 'Rosales', 'York', 'Christian', 'Phelps', 'Farrell', 'Castaneda', 'Nash', 'Dickerson', 'Bond', 'Wyatt', 'Foley', 'Chase', 'Gates', 'Vincent', 'Mathews', 'Hodge', 'Garrison', 'Trevino', 'Villarreal', 'Heath', 'Dalton', 'Valencia', 'Callahan', 'Hensley', 'Atkins', 'Huffman', 'Roy', 'Boyer', 'Shields', 'Lin', 'Hancock', 'Grimes', 'Glenn', 'Cline', 'Delacruz', 'Camacho', 'Dillon', 'Parrish', 'Oneill', 'Melton', 'Booth', 'Kane', 'Berg', 'Harrell', 'Pitts', 'Savage', 'Wiggins', 'Brennan', 'Salas', 'Marks', 'Russo', 'Sawyer', 'Baxter', 'Golden', 'Hutchinson', 'Liu', 'Walter', 'Mcdowell', 'Wiley', 'Rich', 'Humphrey', 'Johns', 'Koch', 'Suarez', 'Hobbs', 'Beard', 'Gilmore', 'Ibarra', 'Keith', 'Macias', 'Khan', 'Andrade', 'Ware', 'Stephenson', 'Henson', 'Wilkerson', 'Dyer', 'Mcclure', 'Blackwell', 'Mercado', 'Tanner', 'Eaton', 'Clay', 'Barron', 'Beasley', 'Oneal', 'Small', 'Preston', 'Wu', 'Zamora', 'Macdonald', 'Vance', 'Snow', 'Mcclain', 'Stafford', 'Orozco', 'Barry', 'English', 'Shannon', 'Kline', 'Jacobson', 'Woodard', 'Huang', 'Kemp', 'Mosley', 'Prince', 'Merritt', 'Hurst', 'Villanueva', 'Roach', 'Nolan', 'Lam', 'Yoder', 'Mccullough', 'Lester', 'Santana', 'Valenzuela', 'Winters', 'Barrera', 'Orr', 'Leach', 'Berger', 'Mckee', 'Strong', 'Conway', 'Stein', 'Whitehead', 'Bullock', 'Escobar', 'Knox', 'Meadows', 'Solomon', 'Velez', 'Odonnell', 'Kerr', 'Stout', 'Blankenship', 'Browning', 'Kent', 'Lozano', 'Bartlett', 'Pruitt', 'Buck', 'Barr', 'Gaines', 'Durham', 'Gentry', 'Mcintyre', 'Sloan', 'Rocha', 'Melendez', 'Herman', 'Sexton', 'Moon', 'Hendricks', 'Rangel', 'Stark', 'Lowery', 'Hardin', 'Hull', 'Sellers', 'Ellison', 'Calhoun', 'Gillespie', 'Mora', 'Knapp', 'Mccall', 'Morse', 'Dorsey', 'Weeks', 'Nielsen', 'Livingston', 'Leblanc', 'Mclean', 'Bradshaw', 'Glass', 'Middleton', 'Buckley', 'Schaefer', 'Frost', 'Howe', 'House', 'Mcintosh', 'Ho', 'Pennington', 'Reilly', 'Hebert', 'Mcfarland', 'Hickman', 'Noble', 'Spears', 'Conrad', 'Arias', 'Galvan', 'Velazquez', 'Huynh', 'Frederick', 'Randolph', 'Cantu', 'Fitzpatrick', 'Mahoney', 'Peck', 'Villa', 'Michael', 'Donovan', 'Mcconnell', 'Walls', 'Boyle', 'Mayer', 'Zuniga', 'Giles', 'Pineda', 'Pace', 'Hurley', 'Mays', 'Mcmillan', 'Crosby', 'Ayers', 'Case', 'Bentley', 'Shepard', 'Everett', 'Pugh', 'David', 'Mcmahon', 'Dunlap', 'Bender', 'Hahn', 'Harding', 'Acevedo', 'Raymond', 'Blackburn', 'Duffy', 'Landry', 'Dougherty', 'Bautista', 'Shah', 'Potts', 'Arroyo', 'Valentine', 'Meza', 'Gould', 'Vaughan', 'Fry', 'Rush', 'Avery', 'Herring', 'Dodson', 'Clements', 'Sampson', 'Tapia', 'Bean', 'Lynn', 'Crane', 'Farley', 'Cisneros', 'Benton', 'Ashley', 'Mckay', 'Finley', 'Best', 'Blevins', 'Friedman', 'Moses', 'Sosa', 'Blanchard', 'Huber', 'Frye', 'Krueger', 'Bernard', 'Rosario', 'Rubio', 'Mullen', 'Benjamin', 'Haley', 'Chung', 'Moyer', 'Choi', 'Horne', 'Yu', 'Woodward', 'Ali', 'Nixon', 'Hayden', 'Rivers', 'Estes', 'Mccarty', 'Richmond', 'Stuart', 'Maynard', 'Brandt', 'Oconnell', 'Hanna', 'Sanford', 'Sheppard', 'Church', 'Burch', 'Levy', 'Rasmussen', 'Coffey', 'Ponce', 'Faulkner', 'Donaldson', 'Schmitt', 'Novak', 'Costa', 'Montes', 'Booker', 'Cordova', 'Waller', 'Arellano', 'Maddox', 'Mata', 'Bonilla', 'Stanton', 'Compton', 'Kaufman', 'Dudley', 'Mcpherson', 'Beltran', 'Dickson', 'Mccann', 'Villegas', 'Proctor', 'Hester', 'Cantrell', 'Daugherty', 'Cherry', 'Bray', 'Davila', 'Rowland', 'Madden', 'Levine', 'Spence', 'Good', 'Irwin', 'Werner', 'Krause', 'Petty', 'Whitney', 'Baird', 'Hooper', 'Pollard', 'Zavala', 'Jarvis', 'Holden', 'Haas', 'Hendrix', 'Mcgrath', 'Bird', 'Lucero', 'Terrell', 'Riggs', 'Joyce', 'Mercer', 'Rollins', 'Galloway', 'Duke', 'Odom', 'Andersen', 'Downs', 'Hatfield', 'Benitez', 'Archer', 'Huerta', 'Travis', 'Mcneil', 'Hinton', 'Zhang', 'Hays', 'Mayo', 'Fritz', 'Branch', 'Mooney', 'Ewing', 'Ritter', 'Esparza', 'Frey', 'Braun', 'Gay', 'Riddle', 'Haney', 'Kaiser', 'Holder', 'Chaney', 'Mcknight', 'Gamble', 'Vang', 'Cooley', 'Carney', 'Cowan', 'Forbes', 'Ferrell', 'Davies', 'Barajas', 'Shea', 'Osborn', 'Bright', 'Cuevas', 'Bolton', 'Murillo', 'Lutz', 'Duarte', 'Kidd', 'Key', 'Cooke'];
    const LOOT_NAMES = ['Station', 'Park', 'Kasteel', 'Skibaan', 'Museum', 'Albert Heijn', 'Jumbo', 'Coop', 'Spar', 'Hotel', 'Sportcentrum', 'Zwembad', 'Gym', 'Action', 'Blokker', 'Kruidvat', 'Coffeeshop', 'Shell', 'TinQ', 'HEMA', 'Praxis', 'Kwantum', 'Ikea', 'Golfbaan', 'Ziekenhuis', 'Dierenarts', 'Nettorama', 'BP', 'Universiteit', 'School', 'Restaurant', 'Snackbar'];
    const USER_ROLES = [Roles::Thief, Roles::Police];
    const ALPHANUMERIC_CAPITALS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    public function run($amount_of_games = 5, $amount_of_users_per_game = 4, $amount_of_loot_per_game = 5)
    {
        if ($amount_of_games > 20)
            $amount_of_games = 20;
        if ($amount_of_users_per_game > 20)
            $amount_of_users_per_game = 20;
        if ($amount_of_loot_per_game > 20)
            $amount_of_loot_per_game = 20;

        $first_available_id = Game::all()->last()->id + 1;

        for ($i = 0; $i < $amount_of_games; $i++) {
            $id = $first_available_id + $i;

            $keys = array();
            $users = new Collection();
            for ($x = 0; $x < $amount_of_users_per_game; $x++) {
                $user = User::create([
                    'username' => self::USERNAMES[rand(0, count(self::USERNAMES) - 1)],
                    'location' => $this->getRandLocationNearOss()
                ]);
                $users->push($user);
            }

            $game = Game::create([
                'id' => $id,
                'police_station_location' => $this->getRandLocationNearOss()
            ]);

            $game->thieves_score = rand(0, 1000);
            $game->police_score = rand(0, 1000);
            $game->save();

            for ($x = 0; $x < $amount_of_loot_per_game; $x++) {
                Loot::create([
                    'lootable_id' => $game->id,
                    'lootable_type' => Game::class,
                    'name' => self::LOOT_NAMES[rand(0, count(self::LOOT_NAMES) - 1)],
                    'location' => $this->getRandLocationNearOss()
                ]);
            }

            BorderMarker::create([
                'borderable_id' => $game->id,
                'borderable_type' => Game::class,
                'location' => $this->getRandLocationForMarkersNearOss(1)
            ]);
            BorderMarker::create([
                'borderable_id' => $game->id,
                'borderable_type' => Game::class,
                'location' => $this->getRandLocationForMarkersNearOss(2)
            ]);
            BorderMarker::create([
                'borderable_id' => $game->id,
                'borderable_type' => Game::class,
                'location' => $this->getRandLocationForMarkersNearOss(3)
            ]);
            BorderMarker::create([
                'borderable_id' => $game->id,
                'borderable_type' => Game::class,
                'location' => $this->getRandLocationForMarkersNearOss(4)
            ]);
            BorderMarker::create([
                'borderable_id' => $game->id,
                'borderable_type' => Game::class,
                'location' => $this->getRandLocationForMarkersNearOss(5)
            ]);

            $keys = array();
            $invite_keys = new Collection();
            for ($x = 0; $x < $amount_of_users_per_game + 2; $x++) {
                $key = $this->createKeyString($keys);
                array_push($keys, $key);

                $db_key = InviteKey::create([
                    'value' => $key,
                    'game_id' => $id,
                    'role' => self::USER_ROLES[rand(0, 1)]
                ]);
                $invite_keys->push($db_key);
            }

            for ($x = 0; $x < $amount_of_users_per_game; $x++) {
                $invite_keys[$x]->user_id = $users[$x]->id;
                $invite_keys[$x]->save();
            }
        }
    }

    private function getRandLocationNearOss()
    {
        $latitude = "51.7" . strval(rand(43866, 79043));
        $longitude = "5." . strval(rand(491387, 553818));
        return $latitude . ',' . $longitude;
    }

    private function createKeyString($keys)
    {
        $key = "";
        while ($key == "" || in_array($key, $keys)) {
            $key = "";
            for ($j = 0; $j < 4; $j++) {
                $key .= self::ALPHANUMERIC_CAPITALS[rand(0, (count(self::ALPHANUMERIC_CAPITALS) - 1))];
            }
        }
        return $key;
    }

    private function getRandLocationForMarkersNearOss($number)
    {
        switch($number) {
            case 1:
                return "51.7" . strval(rand(30421, 34703)) . ",5." . strval(rand(470841, 482090));
            case 2:
                return "51.7" . strval(rand(31104, 35002)) . ",5." . strval(rand(593363, 598034));
            case 3:
                return "51.7" . strval(rand(90136, 93482)) . ",5." . strval(rand(576329, 579863));
            case 4:
                return "51.7" . strval(rand(76128, 79084)) . ",5." . strval(rand(528871, 543762));
            case 5:
                return "51.7" . strval(rand(80532, 82789)) . ",5." . strval(rand(474034, 497632));
            default:
                return "51.7" . strval(rand(30000, 33000)) . ",5." . strval(rand(470000, 480000));
        }
    }
}
