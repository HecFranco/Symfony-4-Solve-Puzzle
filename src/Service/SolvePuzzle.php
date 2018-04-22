<?php
// src/Service/EvalueFile.php
namespace App\Service;

class SolvePuzzle {
  public function SolvePuzzle($numberRowsColumns, $array_data) {
    $puzzle = $this->Pieces($array_data);
    echo "DELIVERED PUZZLE OF ".$numberRowsColumns['rows']." X ".$numberRowsColumns['columns']." PIECES <br>";
    $limit_rows = $numberRowsColumns['rows']-1;
    $limit_columns = $numberRowsColumns['columns']-1;
    $solution = array();
    $number_solutions = 1;
    $tried = 0;
    $new_tried = 0;
    $first_solution = false;
    // limit positions [column, row]
    // [A, B, C, D] -> [side left, side up, side right, side down]
    // example [1, 1] -> [0, 0, ?, ?]
    // example [1, 4] -> [?, 0, 0, ?]
    // example [4, 1] -> [0, ?, ?, 0]
    // example [4, 4] -> [?, ?, 0, 0]
    echo "First piece used, ";
    $firstPiece = $this->firstPiece ($puzzle);
    echo "in the position: ";
    foreach ($firstPiece as $number_column=>$column){
      echo $number_column."-";
      foreach ($column as $number_row=>$row){
        echo $number_row.", ";
        foreach ($row as $number_piece=>$piece){
          if(intval($number_piece)){
            echo "is: ".$number_piece;
            echo "<br>Its position is: ";
          }else{
            foreach ($piece as $side=>$value) {
              echo $side." -> ".$value.", ";
            }
            echo "<br>--------------------------------------------------------<br>";
          }
        }
      }
    }
    $solution_array = array();
    array_push($solution_array, $firstPiece);
    echo "<h3>Tried number: ".$tried."</h3>";
    while ($tried <= $number_solutions){
      for ($row = 0 ; $row <= $limit_rows ; $row++){
        for ($column = 0 ; $column <= $limit_columns ; $column++ ){
          if( !($column === 0 && $row === 0) ){
            echo "<br> We look for the new piece configuration: <br>";
            $new_configuration = $this->newConfiguration($solution_array[$tried], $limit_rows, $limit_columns, $row, $column);
            echo "<br>We have used the pieces, for the position ".$column." - ".$row."...<br>";
            $pieces_using = $this->piecesUsing($solution_array[$tried]);
            echo "<br>We have found the following pieces...<br>";
            $find_pieces = $this->findPieces($new_configuration, $puzzle);
            $total_pieces_puzzle = ( $limit_rows+1 ) * ( $limit_columns+1 );
            $total_pieces_using = count($pieces_using);
            echo "<br>Total number Pieces used: ".$total_pieces_using;
            $number_find_pieces = count($find_pieces);
            echo "<br>Total number Pieces find: ".$number_find_pieces;
            $first_find_piece = true;
            $number_valid_pieces = 0;
            foreach($find_pieces as $number_possible_piece => $possible_piece){
              $exist_piece = $this->existPiece($pieces_using, $possible_piece);
              if ( !$exist_piece ){
                $number_valid_pieces++;
                if ( $first_find_piece === true ){
                  echo "<br>Possible next piece: "; 
                  foreach($possible_piece as $key=>$value){if(intval($key)){echo $key."<br>";}}
                  $solution_array[$tried][$column][$row] = $possible_piece;
                  $first_find_piece = false;
                }else{
                  $new_tried++;
                  echo "¡¡¡New possible solution!!! Solution number: ".$new_tried;
                  echo ", this will be: ";foreach($pieces_using as $key=>$value){echo $value.", ";} 
                  echo " and ";
                  foreach($possible_piece as $key=>$value){if(intval($key)){echo $key."<br>";}}
                  $solution_array[$new_tried] = $solution_array[$tried];
                  $solution_array[$new_tried][$column][$row] = $possible_piece;
                }
              }
            }
            echo "<br>--------------------------------------------------------<br>";
            echo "Total number Pieces find valid: ".$number_valid_pieces;
            echo "<br>--------------------------------------------------------<br>";
            if($number_valid_pieces === 0){
              $number_possible_pieces = 0;
              foreach($find_pieces as $number_possible_piece => $possible_piece){
                $exist_piece = $this->existPiece($pieces_using, $possible_piece);
                if ( !$exist_piece ){
                  $number_possible_pieces++;
                }
                  if($number_possible_pieces === 0){
                    echo "<br>--------------------------------------------------------<br>";
                    echo "SOLUTION COMPLETE!!!!!!!!!!";
                    if ( $total_pieces_puzzle > $total_pieces_using ){
                      echo "  SOLUTION NOT VALID!!!";
                    }else{
                      echo "  SOLUTION VALID!!!";
                    }
                    echo "<br>--------------------------------------------------------<br>";
                    $row = $limit_rows; $column = $limit_columns;
                    $tried++;
                    echo "<br>--------------------------------------------------------<br>";
                    echo "Tried number: ".$tried;
                    echo "<br>--------------------------------------------------------<br>";
                  }
              }
            }           
          }
        }
      }
    }
    die();
  }
  public function Pieces($array_data) {
    unset($array_data[0]);
    unset($array_data[1]);
    $pieces = array_chunk($array_data, 4);
    return $pieces;
  }
  public function RotatePiece($piece){
    $options = array();
    foreach ($piece as $key=>$value) {
      $options[$key] = array();
      for ( $i = 0; $i <= 3 ; $i++ ){
        $side = ($key+$i > 3)? $key+$i-4 : $key+$i ;
        if ($i == 0){ $options[$key]['A'] = (int) $piece[$side]; }
        if ($i == 1){ $options[$key]['B'] = (int) $piece[$side]; }
        if ($i == 2){ $options[$key]['C'] = (int) $piece[$side]; }
        if ($i == 3){ $options[$key]['D'] = (int) $piece[$side]; }
      }
    }
    return $options;
  }
  public function PieceItsOk ($configuration, $rotation_value){
    $coincidence = 0 ;
    foreach($configuration as $side => $value){
      if ( $rotation_value[$side] === $value || $value === null ){
        $coincidence++;
      }
    }
    $piece_its_ok = ($coincidence === 4)?true:false;
    return $piece_its_ok;
  }
  public function howMany ($array, $search){
    $count = 0;
    foreach($array as $key => $value){
      if($value === $search){
        $count++;
      }
    }
    return $count;
  }
  public function firstPiece ($puzzle){
    $solution = array();
    foreach($puzzle as $piece_number=>$piece_value){
      $rotation_options = $this->RotatePiece($piece_value);
      foreach($rotation_options as $rotation_number=>$rotation_value){
        $configuration = ['A'=>0,'B'=>0,'C'=>null,'D'=>null];
        $piece_its_ok = $this->PieceItsOk($configuration, $rotation_value);
        if($piece_its_ok === true){
          // ['column']['row']
          $new_solution[0][0][$piece_number] = $rotation_number;
          $new_solution[0][0]['conditions'] = $rotation_value;
          //array_push($solution,$new_solution);
          //return $solution;
          return $new_solution;
        }
      }
    }
  }
  public function newConfiguration ($solution_tried, $limit_rows, $limit_columns, $row, $column){
    $A = ($column == 0)? 0 : $solution_tried[$column-1][$row]['conditions']['C'];
    $B = ($row == 0)? 0 : $solution_tried[$column][$row-1]['conditions']['D'];
    $C = ($column == $limit_columns)? 0 : null;
    $D = null;
    echo "A->".$A.", B->".$B.", C->".(($C !== null)? $C : "?").", D->?";
    $configuration = ['A'=>$A,'B'=>$B,'C'=>$C,'D'=>$D];
    return $configuration;
  }
  public function findPieces($new_configuration, $puzzle){
    $new_pieces_list = array();
    foreach($puzzle as $piece_number=>$piece_value){
      $rotation_options = $this->RotatePiece($piece_value);
      foreach($rotation_options as $rotation_number=>$rotation_value){
        $piece_its_ok = $this->PieceItsOk($new_configuration, $rotation_value);
        if($piece_its_ok === true){
          // ['number of solution']['column']['row']
          $solution = [
            $piece_number => $rotation_number, 
            'conditions' => $rotation_value
          ];
          echo $piece_number.", ";
          array_push($new_pieces_list, $solution);
        }
      }
    }
    return $new_pieces_list;
  }
  public function piecesUsing($solution_tried){
    $piecesUsing = array();    
    foreach($solution_tried as $column=>$value_column){
      foreach($value_column as $row=>$value_row){
        $key_search = true;
        foreach($value_row as $key=>$value){
          if($key_search){
            $key_search = false;
            echo $key.", ";
            array_push($piecesUsing, $key);
          }
        }
      }
    }
    return $piecesUsing;
  }
  public function existPiece($pieces_using, $possible_piece){
    $existPiece = false;
    foreach($pieces_using as $key => $value){
      if ( array_key_exists($value, $possible_piece) ){
        $existPiece = true;
      }
    }
    return $existPiece;
  }
}