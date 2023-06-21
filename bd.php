<?php
require './libs/rb-mysql.php';

R::setup( 'mysql:host=localhost;dbname=recipes', 'login', 'password');

if (!R::testConnection())
{
	echo 'Не удалось подключится к базе данных';
	exit;
}

function register_user($login, $password) 
{
  if (empty($login) || strlen($login) > 50 || preg_match('/[^a-zA-Z0-9_-]/', $login)) {
    return "Ошибка: некорректный логин";
  }
  if (empty($password) || strlen($password) < 8) {
    return "Ошибка: пароль должен содержать не менее 8 символов";
  }

  $login = mysqli_real_escape_string($R, $login);
  $hash = hash('sha256', $password);
  $result = mysqli_query($R, "SELECT id FROM users WHERE login = '$login'");
  if (mysqli_num_rows($result) > 0) {
    return "Пользователь с логином $login уже есть в системе";
  }
  $result = mysqli_query($R, "INSERT INTO users (login, password) VALUES ('$login', '$hash')");
  if (!$result) {
    return "Ошибка: не удалось создать пользователя";
  }
  return "Пользователь $login успешно зарегистрирован!";
} 

function check_user_pass($login, $password) {
  $user = R::findOne('users', 'login = ?', [$login]);
  if ($user == null) {
    return 'Такого пользователя нет';
  }
  
  $hashedPassword = $user->pass;
  
  if (password_verify($password, $hashedPassword)) 
  {
    return "true";
  } 
  else 
  {
	return "false";
  }
} 

function get_recipes()
{
	$recipes = R::getAll(
    'SELECT * FROM recipes');
	return json_encode($recipes, JSON_UNESCAPED_UNICODE);
}

function delete_user_recipe($login, $nameRecipe) 
{
	$user = R::findOne('users', 'login = ?', [$login]);
	if (!$user) 
	{
		return "Такого пользователя нет";
	}

	$recipe = R::findOne('recipes', 'name = ?', [$nameRecipe]);
	if (!$recipe) 
	{
		return "Такого рецепта нет";
	}

	$userRecipe = R::findOne('usersrecipes', 'user_id = ? AND recipe_id = ?', [$user->id, $recipe->id]);
	if (!$userRecipe) 
	{
		return "Такого рецепта нет у пользователя";
	}

	R::trash($userRecipe);
	R::trash($recipe);

	return "true";
}

function change_user_recipe($login, $nameRecipe, $recipeJson) {
  $user = R::findOne('users', 'login = ?', [$login]);

  if (!$user) 
  {
    return "Такого пользователя нет";
  }

  $recipe = R::findOne('recipes', 'name = ?', [$nameRecipe]);

  if (!$recipe) 
  {
    return "Такого рецепта нет";
  }

  $userRecipe = R::findOne('usersrecipes', 'user_id = ? AND recipe_id = ?', [$user->id, $recipe->id]);

  if (!$userRecipe) 
  {
    return "У пользователя нет такого рецепта";
  }

  $recipeFromClient = json_decode($recipeJson, true);

  $recipe->name = $recipeFromClient["name"];
  $recipe->content = $recipeFromClient["content"];
  $recipe->description = $recipeFromClient["description"];
  $recipe->products = $recipeFromClient["products"];
  $recipe->owner = $login;

  R::store($recipe);

  return "true";
}


function get_user_recipes($login)
{
	$user = R::findOne('users','login = ?', array($login));
	if (!isset($user)) 
		return "Такого пользователя нет";
	
	$recipes = R::find('usersrecipes','user_id = ?', array($user->id));
	
	
	if (!isset($recipes) || empty($recipes)) 
		return json_encode(array_values($recipes), JSON_UNESCAPED_UNICODE);
	
	foreach ($recipes as $recipe) 
	{
		$ids[] = $recipe->recipe_id;
	}
	 $ids = array($ids);
	$new_recipes = R::find('recipes', 'id IN (:indexes)', [':indexes' => $ids]);

	return json_encode(array_values($new_recipes), JSON_UNESCAPED_UNICODE);
}

function add_user_recipe($login, $recipeId)
{
	$user = R::findOne('users','login = ?', array($login));
	$user_recipe = R::findOne('recipes','id = ?', array($recipeId));
	
	if (!isset($user)) 
		return "Такого пользователя нет";
	if (!isset($user_recipe)) 
		return "Такого рецепта нет";
	
	$new_user_recipe = R::dispense('usersrecipes');
		
	$new_user_recipe->recipe_id = $user_recipe->id;
    $new_user_recipe->user_id = $user->id;
    $id = R::store( $new_user_recipe );
	
	return $id;
}

function add_recipe($login, $recipeJson)
{
	$user = R::findOne('users','login = ?', array($login));
	if (!isset($user)) 
		return "Такого пользователя нет";
	
	$recipe = json_decode($recipeJson, true);
	
	$new_recipe = R::dispense( 'recipes' );	
	

	$new_recipe->name = $recipe["name"];
	$new_recipe->description = $recipe["description"];
    $new_recipe->content = $recipe["content"];
	
	$new_recipe->products = $recipe["products"];
    $new_recipe->owner = $login;
    $id = R::store($new_recipe);
	
	return $id;
}

?>