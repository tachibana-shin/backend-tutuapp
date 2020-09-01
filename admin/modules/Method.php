<?php
   class Method {
      static public function unset_cache(&$array) {
         for ( $i = 0, $count = count($array); $i < $count; $i++ ) {
            unset($array[$i]);
         }
      }
   }
?>