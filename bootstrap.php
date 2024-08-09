<?php

use AmoCRM\Client\AmoCRMApiClient;

include_once './vendor/autoload.php';

$clientId = '66889795-d955-49bf-a949-89c12a725ae7';
$clientSecret = 'vO5rL02ryR52d7euvZR2Eyhnk7yw1FcBu7prd5tS2zbu1EBlkKDIkRrv9Q7NDAm6';
$redirectUri = 'https://05aa-176-52-42-76.ngrok-free.app';

$apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);

include_once 'token_actions.php';
include_once 'error_printer.php';