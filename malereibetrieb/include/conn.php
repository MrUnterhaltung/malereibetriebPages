<?php
    include("config.php");

    $conn = new mysqli($hostname, $username, $password, $db);

    if ($debug) {

      if ($conn->connect_error) {
        die("<div class='debug'>Verbindung zur Datenbank fehlgeschalgen: " . $conn->connect_error . "</div>");

      } else {
        echo("<div class='debug'>Verbindung zur Datenbank hergestellt</div>");
      }

    }
?>