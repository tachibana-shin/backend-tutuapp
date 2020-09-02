<?php
   require_once __DIR__."/../modules/SQL.php";
   require_once __DIR__."/../modules/OAuth.php";
   
   new class {
      public function __construct() {
         if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
            if ( OAuth::authorized() ) {
               $this -> checkPassword();
            } else {
               echo json_encode(ErrorMS::LOGIN);
            }
         }
      }
      
      private function checkPassword() {
         global $SQL;
         if ( !empty($_POST["password"] ?? null) ) {
            $email = OAuth::getToken() -> email;
            $password = md5($_POST["password"]);
            echo json_encode([
               "state" => [
                  "error" => false,
                  "code" => 200,
                  "message" => "Request success"
               ],
               "data" => $SQL -> query("select * from AdminAccount where email = '".addslashes($email)."', password = '$password'") -> num_rows > 0
            ]);
         } else {
            echo json_encode(ErrorMS::PARAMS);
         }
      }
   }
?>