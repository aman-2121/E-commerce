<?php
$password = 3333533;
$password = password_hash($password, PASSWORD_DEFAULT);
echo $password;
?>