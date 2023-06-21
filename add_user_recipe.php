<?php
require 'bd.php';

$login = $_POST['Login'];
$recipeId =  $_POST['Id'];

if (!isset($login) || !isset($recipeId))
{
	echo "error login-RecipeId";
	exit;
}

echo add_user_recipe($login, $recipeId)
?>