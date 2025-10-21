<?php
$passwords = [
    "ad_thanu1",      // password for thanushya Admin
    "ct_piyula1",  // password for piyula Caretaker
    "hr_satheeshan1",
    "hr_nanduni1",
    "hr_amana1"          // password for satheeshan HR
];

foreach ($passwords as $pass) {
    echo "Password: $pass => Hashed: " . password_hash($pass, PASSWORD_DEFAULT) . "\n";
}
?>
