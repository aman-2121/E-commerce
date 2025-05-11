<?php
$password = 333333;
$password = password_hash($password, PASSWORD_DEFAULT);
echo $password;
?>