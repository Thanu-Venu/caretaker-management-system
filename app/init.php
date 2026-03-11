<?php
session_start();  // Start session for the whole app

require_once 'core/App.php';
require_once 'core/Controller.php';
require_once 'core/Database.php';
require_once 'config/config.php';
require_once 'core/AuthSession.php';

AuthSession::bootstrap();
$app = new App();