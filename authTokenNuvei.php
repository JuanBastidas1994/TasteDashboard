<?php
$serverKey = "SAMBOLONPTOSANTANA-EC-SERVER";
  $serverCode = "N4q1tqN7zxVZbFIMQ9ux8BMrCRMoqE";
  $date = new DateTime();
  $unixTimestamp = $date->getTimestamp();
  $uniqTokenHash = hash('sha256', $serverKey.$unixTimestamp);
  $authToken = base64_encode("{$serverCode};{$unixTimestamp};{$uniqTokenHash}");
    echo $authToken;