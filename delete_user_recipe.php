<?php
require 'bd.php';


$login = $_POST['Login'];
$recipeName =  $_POST['RecipeName'];

if (!isset($login) || !isset($recipeName))
{
	echo "error login-recipeName";
	exit;
}

echo delete_user_recipe($login, $recipeName)
?>