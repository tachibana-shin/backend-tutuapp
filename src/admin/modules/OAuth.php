<?php
   require_once __DIR__."/JWT.php";
   require_once __DIR__."/SQL.php";
   require_once __DIR__."/Method.php";
   
   use \Firebase\JWT\JWT;
   
   class OAuth {
      const KEY_JWT = "MY_KEY_JWT";
      
      const TABLE_AUTHORLIZER = "AdminAccount";
      
      static public function getToken() {
         $token = $_COOKIE["authorlize_token"] ?? null;
            
         return $token == null ? null : JWT::decode($token, self::KEY_JWT, ["HS256"]);
      }
      static public function setToken($payload, string $timedie = "+30days") {
         $token = (string) JWT::encode($payload, self::KEY_JWT);
         JWT::$leeway = strtotime($timedie);
         setcookie("authorlize_token", $token, strtotime($timedie), "/", null, null, true);
      }
      static public function authorized() {
         return !empty(self::getToken());
      }
      static public function Login(string $email, string $password) {
         global $SQL;
         
         $user = $SQL -> query("select * from ".self::TABLE_AUTHORLIZER." where email = '".addslashes($email)."' and password = '".md5($password)."'");

         if ( $user -> num_rows > 0 ) {
            $_user = $user -> fetch_array();
            $user -> free_result();
            unset($_user["password"]);
            Method::unset_cache($_user);
            self::setToken($_user);
            /* save authorlize for cookie http */
            return $_user;
         } else {
            return null;
         }
      }
      static public function Logout() {
         return setcookie("authorlize_token", "", -1, "/", null, null, true);
      }
      static private function checkEmail(string $email) {
         global $SQL;
         return $SQL -> query("select * from ".self::TABLE_AUTHORLIZER." where email = '".addslashes($email)."'") -> num_rows > 0;
      }
      static public function ResetPassword(string $email) {
         global $SQL;
         $user = $SQL -> query("select email from ".self::TABLE_AUTHORLIZER." where email = '".addslashes($email)."'");
         
         if ( $user -> num_rows > 0 ) {
            $_user = $user -> fetch_array();
            $user -> free_result();
            $token = (string) JWT::encode($_user, self::KEY_JWT);
            JWT::$leeway = strtotime("+15 minutes");
            return $token;
         } else {
            return null;
         }
         
      }
      const REGISTER_EMAIL = "REGISTER_EMAIL";
      static public function Resign($email, $password, $displayName = null, $photoURL = null) {
         global $SQL;
         if ( self::checkEmail($email) ) {
            return self::REGISTER_EMAIL;
         } else {
            if ( $SQL -> query("insert into ".self::TABLE_AUTHORLIZER." (email, password, displayName, photoURL) values ('"
               .addslashes($email)."', '"
               .md5($password)."', '"
               .addslashes($displayName)."', '"
               .addslashes($photoURL)."')")
            ) {
               return [
                  "email" => $email,
                  "displayName" => $displayName,
                  "photoURL" => $photoURL
               ];
            }
         }
      }
   }
?>