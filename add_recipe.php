<?php
require 'bd.php';

$login = $_POST['Login'];
$recipeJson =  $_POST['RecipeJson'];

if (!isset($login) || !isset($recipeJson))
{
	echo "error login-recipeJson";
	exit;
}

echo add_recipe($login, $recipeJson)
?>