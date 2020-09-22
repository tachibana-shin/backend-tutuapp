<?php
 
$CHAR_128 = [" ", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", " ", "¡", "¢", "£", "¤", "¥", "¦", "§", "¨", "©", "ª", "«", "¬", "­", "®", "¯", "°", "±", "²", "³", "´", "µ", "¶", "·", "¸", "¹", "º", "»", "¼", "½", "¾", "¿", "À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï", "Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "×", "Ø", "Ù", "Ú", "Û", "Ü", "Ý", "Þ", "ß", "à", "á", "â", "ã", "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï", "ð", "ñ", "ò", "ó", "ô", "õ", "ö", "÷", "ø", "ù", "ú", "û", "ü", "ý", "þ", "ÿ"];

function fromCharCode( int $index ) {
    global $CHAR_128;
    return $index < 128 ? chr($index) : $CHAR_128[ $index - 128 ];
}
function charCodeAt( string $string ) {
   global $CHAR_128;
   return array_flip($CHAR_128)[ $string ] ?? ord($string);
}

class Base {
   static public function encode( string $string, int $express = 60 ) {
      $key = rand(0, 0xff);
      
      return join("@", [
          base_convert( $key, 10, 16 ), 
          join("", array_map(function ( $item ) use ( $key ) {
              $charCode = charCodeAt($item) + $key;
         
              if ( $charCode > 0xff ) {
                 return fromCharCode($charCode - floor($charCode / 0xff) * 0xff).".".floor($charCode / 0xff);
              } else {
                 return fromCharCode($charCode);
              }
          }, str_split($string))),
          base_convert(time() + $express, 10, 16)
       ]);
   }
   static public function decode( string $string ) {
      
      list($key, $payload, $express) = explode("@", $string);
      
      $key = intval( $key, 16 );
      $express = intval( $express, 16 );
      
      if ( $express < time() ) {
         throw new Error("myBase: timeout!");
      }
      
      preg_match_all("/\S(?:\.\d)?/", $string, $charMap);
      
      return join("", array_map(function ( $item ) use ( $key ) {
          $charCode;
          if ( strpos(".", $item) !== false ) {
              $charCode = charCodeAt(explode(".", $item)[0]) + (int) explode(".", $item)[1] * 0xff - $key;
          } else {
              $charCode = charCodeAt($item) - $key;
          }
          
          return fromCharCode($charCode);
      }, $charMap[0]));
   }
}
?>
