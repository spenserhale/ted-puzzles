<?php

function printMessage ($message) {

    echo("<p>" . $message . "</p>");
}

function printDebug ($var) {

    echo("<pre>" . var_export($var, true) . "</pre>");
}

class Time {

    public $limit = 17;
    public $total = 0;

    public function add($number) {
        $this->total += $number;
    }

    public function isPastLimit() {

        return ( $this->total > $this->limit )
            ? true
            : false
            ;

    }
}

class Person {

    public $name;
    public $speed;

    public function __construct($name, $speed)
    {
        $this->name = $name;
        $this->speed = $speed;
    }

}

class Bridge {

    public $peopleInDanger;
    public $peopleInSafety;
    public $limit = 2;

    public function __construct($peopleInDanger, $peopleInSafety)
    {
        $this->peopleInDanger = $peopleInDanger;
        $this->peopleInSafety = $peopleInSafety;
    }

    public function cross(array $people, $direction) {

        $time = [];
        $peopleName = [];

        switch ( $direction ) {
            case 'toSafety':

                foreach($people as $person) {

                    printMessage('Person: ' . $person->name);

                    $this->peopleInSafety[$person->name] = $person;
                    unset($this->peopleInDanger[$person->name]);
                    array_push( $time, $person->speed);
                    array_push( $peopleName, $person->name);
                }

                printMessage( implode(' and ', $peopleName) . ' moved to Safety taking ' . max( $time ) . ' minutes.' );

                break;

            case 'toDanger':

                foreach($people as $person) {

                    printMessage('Person: ' . $person->name);

                    $this->peopleInDanger[$person->name] = $person;
                    unset($this->peopleInSafety[$person->name]);
                    array_push( $time, $person->speed);
                    array_push( $peopleName, $person->name);
                }

                printMessage( implode(' and ', $peopleName) . ' moved back to Danger taking ' . max( $time )  );

                break;
        }

        return max( $time );
    }
}

class Game {

    public $bridge;
    public $directions = ['toSafety', 'toDanger'];
    public $direction;
    public $sides = ['safety', 'danger'];
    public $side;
    public $selection;
    public $time;
    public $status;

    public function __construct() {

        $this::setup();

    }

    public function setup() {

        $this->time = new Time;

        $this->status = 'inProgress';

        $inDanger = [];
        $inSafety = [];

        $you = new Person('You', 1);
        $inDanger[$you->name] = $you;

        $assistant = new Person('Assistant', 2);
        $inDanger[$assistant->name] = $assistant;

        $janitor = new Person('Janitor', 5);
        $inDanger[$janitor->name] = $janitor;

        $professor = new Person('Professor', 10);
        $inDanger[$professor->name] = $professor;

        $this->bridge = new Bridge($inDanger, $inSafety);

        printMessage('Game Setup');
    }

    public function getRandomPeopleFromDanger () {

        do {

            $selection = [];
            $selection[] = $this->bridge->peopleInDanger[ array_rand($this->bridge->peopleInDanger, 1) ];
            $selection[] = $this->bridge->peopleInDanger[ array_rand($this->bridge->peopleInDanger, 1) ];

        } while ( $selection[0] === $selection[1] );

        return $selection;
    }

    public function runRobot() {

        printMessage( 'Game Started' );

        printMessage( 'Time: ' . $this->time->total);

        $try = 1;

        while( $this->status === 'inProgress' && $try < 11 )
        {
            printMessage( 'Trade: ' . $try );

            //Get Random Group
            $this->selection = $this->getRandomPeopleFromDanger();

            //Cross
            $this->time->total += $this->bridge->cross($this->selection, 'toSafety');
            printMessage( 'Time: ' . $this->time->total);

            if( $this->time->isPastLimit() ) {

                $this->status = 'Game Lost';
                printMessage( $this->status );
                return false;

            } elseif( empty ($this->bridge->peopleInDanger) ) {

                $this->status = 'Game Won';
                printMessage( $this->status );
                return true;
            }

            //Send one back
            $this->selection = [];
            $this->selection[] = $this->bridge->peopleInSafety[ array_rand($this->bridge->peopleInSafety) ];
            $this->time->total += $this->bridge->cross($this->selection, 'toDanger');
            printMessage( 'Time: ' . $this->time->total);

            if( $this->time->isPastLimit() ) {

                $this->status = 'Game Lost';
                printMessage( $this->status );
                return false;

            }

            $try++;
        }

        switch($this->status) {
            case 'Game Lost':
                printMessage( $this->status );
                return false;
                break;

            case 'Game Won':
                printMessage( $this->status );
                return true;
                break;

            default:
                printMessage( $this->status );
                return false;
        }
    }
}

$Game = new Game();
$game_won = false;

$try = 1;

while( ! $game_won && $try < 101) {

    printMessage( 'Game: ' . $try );

    $Game->setup();
    $game_won = $Game->runRobot();

    $try++;
}

/*class Permutation {
    private $result;

    public function getResult() {
        return $this->result;
    }

    public function permute($source, $permutated=array()) {
        if (empty($permutated)){
            $this->result = array();
        }
        if (empty($source)){
            $this->result[] = $permutated;
        } else {
            for($i=0; $i<count($source); $i++){
                $new_permutated = $permutated;
                $new_permutated[] = $source[$i];
                $new_source =    array_merge(array_slice($source,0,$i),array_slice($source,$i+1));
                $this->permute($new_source, $new_permutated);
            }
        }
        return $this;
    }
}

$arr = array(1,2,3,4,5);
$p = new Permutation();
printDebug($p->permute($arr)->getResult());*/