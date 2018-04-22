<?php
// src/Service/EvalueFile.php
namespace App\Service;

class EvalueFile {
  public function extractData($file){
    $data = fopen($file,'r');
    $data_read = fread($data,filesize($file));
    $array_data = explode(" ", $data_read);
    $new_array_data = array();
    foreach($array_data as $key=>$value){
        if( strpos($value, "\n") && strlen($value)>1 ){
          $explode = explode("\n",$value);
          foreach( $explode as $explode_value ){
            if(is_numeric($explode_value)){
              array_push($new_array_data, $explode_value);
            }
          }
        } elseif( strlen($value) > 0 ) {
          if(is_numeric($value)){
            array_push($new_array_data, $value);
          }                   
        }
    }
    return $new_array_data;
  }
  public function numberRowsColumns($array_data){
    $array_data_elements = count($array_data);
    $sides = intval(4);
    $rows = intval($array_data[0]);
    $columns = intval($array_data[1]);
    if( $array_data_elements-2 == ($sides * $rows * $columns) ){
      return ['rows'=>$rows, 'columns'=>$columns];
    }else{
      return [null, null];
    }
  }
}