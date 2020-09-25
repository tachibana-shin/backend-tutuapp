<?php
   require_once __DIR__."/SQL.php";
   require_once __DIR__."/File.php";
   class Method {
      static public function unset_cache(&$array) {
         for ( $i = 0, $count = count($array); $i < $count; $i++ ) {
            unset($array[$i]);
         }
      }
      static public function fetch_array(&result, $unPath = []) {
         $resultData = [];
         if ( $result -> num_rows > 0 ) {
            while ( $row = $result -> fetch_array() ) {
               static::unset_cache($row);
               foreach ( $unPath as $value ) {
                  $row[ $value ] = File::get($row[ $value ]);
               }
               array_push($resultData, $row);
            }
            $result -> free_result();
         }
         return $resultData;
      }
      static function fetch_query($query, $unPath, ...$args) {
         
         if ( gettype($unPath) != "array" ) {
            array_push($args, $unPath);
         }
         
         for ( $index = count($args); $index > -1; $index-- ) {
            $value = $args[$index];
            $query = str_replace("%".($index + 1), addslashes($value), $query);
         }
         global $SQL;
         
         return static::fetch_array($SQL -> query($query), $unPath);
      }
   }
?>
