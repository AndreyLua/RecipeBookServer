<?php
require 'bd.php';


$login = $_POST['Login'];
$recipeName =  $_POST['RecipeName'];
$recipeJson =  $_POST['RecipeJson'];

if (!isset($login) || !isset($recipeName) || !isset($recipeJson)  )
{
	echo "error login-recipeName-recipeJson";
	exit;
}

echo change_user_recipe($login, $recipeName, $recipeJson)
?>