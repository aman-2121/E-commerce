<?php
$password = 515253;
$password = password_hash($password, PASSWORD_DEFAULT);
echo $password;
?>