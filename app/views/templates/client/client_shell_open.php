<?php
/**
 * Opens the client shell (document head + top bar + sidebar).
 * Set before including: $clientPageTitle (string), optional $clientExtraCss (array of paths under public/css/).
 */
if (!isset($clientPageTitle)) {
    $clientPageTitle = 'SmartCare — Client';
}
if (!isset($clientExtraCss) || !is_array($clientExtraCss)) {
    $clientExtraCss = [];
}
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';
