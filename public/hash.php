<?php
echo password_hash("thanu@gmail.com", PASSWORD_DEFAULT);
echo "<br>";


$password = password_hash("123", PASSWORD_DEFAULT);
echo $password;
?>
