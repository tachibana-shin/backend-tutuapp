<?php
   class ErrorMS {
      const PARAMS = [
         "state" => [
            "error" => true,
            "code" => 200,
            "message" => "The parameter passed to the server is wrong."
         ],
         "data" => null
      ];
      const LOGIN = [
         "state" => [
            "error" => true,
            "code" => 200,
            "message" => "You need to be logged in to do this."
         ],
         "data" => null
      ];
      const UNKNOWN = [
         "state" => [
            "error" => true,
            "code" => 200,
            "message" => "Error unknown."
         ],
         "data" => null
      ];
      const APPLICATION_INVALID = [
         "state" => [
            "error" => true,
            "code" => 404,
            "message" => "Application not available."
         ],
         "data" => null
      ];
      const LOGGED = [
         "state" => [
            "error" => true,
            "code" => 200,
            "message" => "You logged."
         ],
         "data" => null
      ];
   }
?>