<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
   header("Content-Type: application/json; charset=utf-8");
    
   require_once __DIR__."/../modules/OAuth.php";
   require_once __DIR__."/../modules/ErrorMS.php";
   
   new class {
      public function __construct() {
          switch ( $_SERVER["REQUEST_METHOD"] ) {
             case "GET":
                echo json_encode([
                   "state" => [
                     "error" => false,
                     "state" => 200,
                     "message" => ""
                   ],
                   "data" => OAuth::getToken()
                ]);
                break;
             case "POST":
                $this -> Login();
                break;
          }
      }
      
      private function checkValid($key) {
         return !empty($_POST[$key] ?? null);
      }
      private function Login() {
         if ( $this -> checkValid("email") && $this -> checkValid("password") ) {
            if ( OAuth::authorized() ) {
               echo json_encode(ErrorMS::LOGGED);
            } else if ( $user = OAuth::Login($_POST["email"], $_POST["password"]) ) {
               echo json_encode([
                  "state" => [
                     "error" => false,
                     "code" => 200,
                     "message" => "Login success"
                  ],
                  "data" => $user
               ]);
            } else {
               echo json_encode(ErrorMS::UNKNOWN);
            }
         } else {
            echo json_encode(ErrorMS::PARAMS);
         }
      }
   }
?>