<?php
   !(function() {
      $protoco = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off" ? "https://" : "http://";
      define("BASE_URI", $protoco.$_SERVER["HTTP_HOST"]);
      define("BASE_URI_REQUEST", BASE_URI.$_SERVER["REQUEST_URI"]);
      define("BASE_URI_QUERY", BASE_URI_REQUEST.$_SERVER["QUERY_STRING"]);
   })();
?>