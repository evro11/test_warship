<?php

class BattleShip
{
    /** 
    *  array with battle field data
    */
    private $battleField;
   
    /**
    * array with ships
    */
    private $ships;
    
    private $test;
    
    
    public function __construct()
    {
        if ( !isset($_SESSION) )  { 
            session_start();
        }
        
        if ( isset($_SESSION['battleField']) ) {
            $this->battleField = $_SESSION['battleField'];
            $this->ships = $_SESSION['ships'];
        }
        
        $this->test = false;
        
        //var_dump($this->battleField);
    } 
    
    
    public function startGame() {
	if (!empty($_POST["data"])) {
            $this->parseData();
            
        }
        else {
            $this->battleField = array();
            $this->ships = array();
            $this->initBattleField();
            $this->initShips();
            
            $_SESSION['battleField'] = $this->battleField;
            $_SESSION['ships'] = $this->ships;
        
            $this->outputPage();
        }
    }
    
    
    public function readTemplate($fileName) {
        $myfile = fopen("tpl" . DS . $fileName, "r") or die("Unable to open file!");
        $html =  fread($myfile,filesize("tpl" . DS . $fileName));
        
        fclose($myfile);
        
        return $html;
    }
    
    public function outputPage() {
        $html =  $this->readTemplate("main.htm");
        
        $table = '<div id="container">' . PHP_EOL;
        for ($row=0; $row<10; $row++) {
            $table .= '<div class="row">' . PHP_EOL;
            for ($column=0; $column<10; $column++) {
                $id = 'r'.$row.'_c'.$column;
                $table .= '<div id="'.$id.'" class="t_column t_empty">';
                $txt = '...';
                if ($this->test) {
                    if ($this->battleField[$row][$column]['free']) {
                        $txt = '0';
                    } else if ( !$this->battleField[$row][$column]['free'] && 
                            isset($this->battleField[$row][$column]['shipName']) ) {
                            $txt = $this->battleField[$row][$column]['shipName'];
                    } else {
                        $txt = '/';
                    }
                }
                $table .= $txt . '</div>' . PHP_EOL;
            }
            $table .= '</div>' .PHP_EOL;
        }

         $table .= '</div>' . PHP_EOL;
        
        $html = str_replace("%battleTable%", $table, $html);
        
        echo $html;
    }
    
    private function initBattleField() {
        for ($row=0; $row<10; $row++) {
            $fieldRow = array();
            for ($column=0; $column<10; $column++) {
                $id = 'r'.$row.'_c'.$column;
                $fieldRow[$column] = array('id' => $id, 'free' => true, 'shot' => false);
            }
            $this->battleField[$row] = $fieldRow;
        }
    }
    
    private function initShips() {
        $allShipsI = $this-> initShipsI();
        $allShipsL = $this->initShipsL();
        $allShipsDot = $this->initShipsDot();

        $shipNames = array();
        for($i = 0; $i < 4; $i++) {
            $shipNames[] = 's' . $i;
        }
        shuffle($shipNames);
        
        // shipI
        $pos = random_int(0, count($allShipsI)); 
        $this->ships[] = array( 'ship' => $allShipsI[$pos], 'name' => $shipNames[0]);
        $this->addToBattleField($allShipsI[$pos], $shipNames[0]);
        
        // shipL
        $pos = $this->findNewShip($allShipsL);
        if ($pos) {
            $this->ships[] = array( 'ship' => $allShipsL[$pos], 'name' => $shipNames[1]);
            $this->addToBattleField($allShipsL[$pos], $shipNames[1]);
        }
        
        // shipDot
        $pos = $this->findNewShip($allShipsDot);
        if ($pos) {
            $this->ships[] = array( 'ship' => $allShipsDot[$pos], 'name' => $shipNames[2]);
            $this->addToBattleField($allShipsDot[$pos], $shipNames[2]);
            unset($allShipsDot[$pos]);
        }
        
        $pos = $this->findNewShip($allShipsDot);
        if ($pos) {
            $this->ships[] = array( 'ship' => $allShipsDot[$pos], 'name' => $shipNames[3]);
            $this->addToBattleField($allShipsDot[$pos], $shipNames[3]);
        }
        
        
        //var_dump($allShipsDot);
        //var_dump($allShipsL);
        
        //var_dump($this->ships);
        //var_dump($this->battleField);
    }
    
    
    /**
    * find ship that we can add
    */ 
    private function findNewShip($shipsArr) {
        for ($i = 0; $i < 100; $i++) {
            $pos = random_int(0, count($shipsArr)-1); 

            if ( !isset($shipsArr[$pos])) {
                continue;
            }
            
            $canAdd = true;

           // control if place is free:
            for ($place = 0; $place < count($shipsArr[$pos]); $place++) {
                $shipPart = $this->getCoordinates($shipsArr[$pos][$place]);
               // print_r($shipPart);
                if (!$this->battleField[$shipPart[0]][$shipPart[1]]['free']) {
                    $canAdd = false;
                    break;
                }
            }
            
            if ($canAdd) {
                return $pos;
            }
        }
        return false;
    }
    
    
    private function initShipsI() {
        $allShipsI = array();
        
        // I shape:
        for ($row=0; $row<10; $row++) {
            for ($column=0; $column<7; $column++) {
                $allShipsI[] = array(
                    'r'.$row.'_c'.$column,
                    'r'.$row.'_c'.($column+1),
                    'r'.$row.'_c'.($column+2),
                    'r'.$row.'_c'.($column+3)
                    );
            }
        }
        for ($row=0; $row<7; $row++) {
            for ($column=0; $column<10; $column++) {
                $allShipsI[] = array(
                    'r'.$row.'_c'.$column,
                    'r'.($row+1).'_c'.$column,
                    'r'.($row+2).'_c'.$column,
                    'r'.($row+3).'_c'.$column
                    );
            }
        }
        
        return $allShipsI;
    }
    
    
    private function initShipsL() {
        $allShipsL = array();
        
        // L shape:
        for ($row=0; $row<10; $row++) {
            for ($column=0; $column<8; $column++) {
                $ship = array(
                    'r'.$row.'_c'.$column,
                    'r'.$row.'_c'.($column+1),
                    'r'.$row.'_c'.($column+2)
                    );
                if ($row > 0) {
                    $shipL = $ship;
                    $shipL[] = 'r'.($row-1).'_c'.$column;
                    $allShipsL[] =$shipL;
                    $shipL = $ship;
                    $shipL[] = 'r'.($row-1).'_c'.($column+2);
                    $allShipsL[] =$shipL;
                }
                 if ($row < 9) {
                    $shipL = $ship;
                    $shipL[] = 'r'.($row+1).'_c'.$column;
                    $allShipsL[] =$shipL;
                    $shipL = $ship;
                    $shipL[] = 'r'.($row+1).'_c'.($column+2);
                    $allShipsL[] =$shipL;
                 }
            }
        }
        for ($row=0; $row<8; $row++) {
            for ($column=0; $column<10; $column++) {
                $ship = array(
                    'r'.$row.'_c'.$column,
                    'r'.($row+1).'_c'.$column,
                    'r'.($row+2).'_c'.$column
                    );
                if ($column > 0) {
                    $shipL = $ship;
                    $shipL[] = 'r'.($row).'_c'.($column-1);
                    $allShipsL[] =$shipL;
                    $shipL = $ship;
                    $shipL[] = 'r'.($row+2).'_c'.($column-1);
                    $allShipsL[] =$shipL;
                }
                if ($column < 9) {
                    $shipL = $ship;
                    $shipL[] = 'r'.($row).'_c'.($column+1);
                    $allShipsL[] =$shipL;
                    $shipL = $ship;
                    $shipL[] = 'r'.($row+2).'_c'.($column+1);
                    $allShipsL[] =$shipL;
                }
            }
        }
        
        return $allShipsL;
    }
    
    
    private function initShipsDot() {
        $allShipsDot = array();
        
        // Dot shape:
        for ($row=0; $row<10; $row++) {
            $fieldRow = array();
            for ($column=0; $column<10; $column++) {
                $id = 'r'.$row.'_c'.$column;
                $allShipsDot[]  = array($id);
            }
        }
        
        return $allShipsDot;
    }
    
    
    private function addToBattleField($ship, $name) {

        for ($i = 0; $i < count($ship); $i++) {
            $pos = $this->getCoordinates($ship[$i]);
           // print_r($pos);
            $fPlace = &$this->battleField[$pos[0]][$pos[1]];
            for ($row = $pos[0]-1; $row <= $pos[0]+1; $row++) {
                for ($col = $pos[1]-1; $col <= $pos[1]+1; $col++) {
                    if (isset($this->battleField[$row][$col]) ) {
                        $this->battleField[$row][$col]['free'] = false;
                    }
                }
            }

            $fPlace['shipName'] = $name;
            //$fPlace['shipPartNr'] = $i;
        }
        //var_dump($ship);
        //var_dump($this->battleField);
    }
    
    
    private function getCoordinates($place) {
        $pos = explode("_c",$place);
        $pos[0] = substr($pos[0], 1, 1);
        return $pos;
    }
    
    
    private function updateShip($name, $shipPartName) {
        $resp = 'hit';
        
        for ($i = 0; $i < count($this->ships); $i++) {
            if ($name == $this->ships[$i]['name']) {
                $ship = &$this->ships[$i]['ship'];
                
                for ($part = 0; $part < count($ship); $part++) {
                    if ($ship[$part] == $shipPartName) {
                        //unset($ship[$part]);
                        array_splice($ship, $part, 1);
                        if (0 == count($ship)) {
                            $resp = 'sunk';
                        }
                        break 2;
                    }
                }
                
            }
        }
        
        return $resp;
    }
    
    
    private function parseData() {
        
        $obj = json_decode($_POST["data"]);
        $respObj = array();
        
        if (!empty($obj->addr)) {
            $coord = $this->getCoordinates($obj->addr);
            if (!isset($this->battleField[$coord[0]][$coord[1]])) {
                $respObj['status'] = 'error';
            }
            else {
                $respObj['data'] = $coord;
                $respObj['status'] = '';
                $fPlace = &$this->battleField[$coord[0]][$coord[1]];
                if (!$fPlace['shot']) {
                    $fPlace['shot'] = true;
                    if (isset($fPlace['shipName'])) {
                        $resp = $this->updateShip($fPlace['shipName'], $fPlace['id']);
                        $respObj['status'] = 'ship_' . $resp;
                        $respObj['shipName'] = $fPlace['shipName'];
                        
                        $respObj['gameOver'] = false;
                        if ($this->isGameOveer()) {
                            $respObj['gameOver'] = true;
                            $respObj['message'] = "Game is over. Refresh the page to start new game";
                        }
                    }
                    else {
                        $respObj['status'] = 'miss';
                    }
                    
                    // save changes
                    $_SESSION['battleField'] = $this->battleField;
                    $_SESSION['ships'] = $this->ships;
                }
                else {
                    $respObj['status'] = 'old_shot';
                }
                
            }
            
        }
        
        echo json_encode($respObj);
    }
    
    
    private function isGameOveer() {
        $resp = true;
        for ($i = 0; $i < count($this->ships); $i++) {
            if (0 < count($this->ships[$i]['ship'])) {
                $resp = false;
            }
        }
        return $resp;;
    }
    
    
}