<?php
   require_once __DIR__."/BASE_URI.php";
   class File {
      const ROOT_PATH = "/../uploaded/";
      const PUBLIC_PATH = __DIR__.self::ROOT_PATH;
      
      static public function save($path = "", $type, $fakepath) {
         if ( !in_array($type, [".jpg", ".jpeg", ".png", ".bin", ".gif"]) ) {
            throw new Error("Server not accept file $type.");
         } else if ( is_file($fakepath) ) {
            $pem = md5_file($fakepath).".".time();
            if ( move_uploaded_file($fakepath, self::PUBLIC_PATH.$path.$pem.$type) ) {
               return $path.$pem.$type;
            } else {
               return null;
            }
         } else {
            throw new Error("$fakepath not file.");
         }
      }
      static public function remove($path) {
         if ( is_file($link = self::PUBLIC_PATH.$path) ) {
            return unlink($link);
         } else {
            return false;
         }
      }
      static public function get($file) {
         return BASE_URI."/admin/modules".self::ROOT_PATH.$file;
      }
      static public function real_path($path) {
         $start = strpos($path, self::ROOT_PATH);
         return $start == null ? $path : substr($path, $start + strlen(self::ROOT_PATH));
      }
   }
?>