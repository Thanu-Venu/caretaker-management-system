<?php
session_start();  // Start session for the whole app

// Set default timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

require_once 'core/App.php';
require_once 'core/Controller.php';
require_once 'core/Database.php';
require_once 'config/config.php';
$app = new App();