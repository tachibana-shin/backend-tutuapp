<?php
   require_once __DIR__."/SQL.php";
   require_once __DIR__."/File.php";
   
   function sameString( string $string, string $keyword ): number {
      for ( $length = count($keyword) - 1; $length > 0; $length-- ) {
         if ( strpos(
            str_replace("/\s/g", "", $string),
            substr(
               str_replace("/\s/g", "", $keyword),
               $length)
            ) !== false ) {
            return $length;
         }
      }
      return 0;
   }
   
   function getPointKeyword( array $app, string $keyword, array $dataField = [] ): number {
      
      $spoint = 0;
      
      foreach ( $dataField as $__proto__ => $value ) {
         $spoint = max( $spoint, sameString($value, $__proto__) * $value );
      }
      
      return $spoint;
   }
    
   function getAppMaxPoint( array $apps, string $keyword ): array {
      $points = array_map(function ( $item ) use ( $keyword ) {
         return [
            "id" => $item["id"],
            "value" => getPointKeyword($item, $keyword, [
               "name" => 70,
               "developer" => 50,
               "description" => 40,
               "category" => 30,
               "compatibility" => 20,
               "languages" => 10
            ])
         ];
      }, $apps);
      
      $max = [
         "id" => null,
         "value" => -Infinite
      ];
      
      foreach ( $points as $index => $value ) {
         if ( $value["point"] > $max["point"] ) {
            $max = $value;
         }
      }
      
      return $max;
   }
   
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
      static public function getAppMaxPoint( array $apps, string $keyword ): array {
         return getAppMaxPoint($apps, $keyword);
      }
   }
?>
