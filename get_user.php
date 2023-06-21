<?php
require 'bd.php';


$login = $_POST['Login'];

if (!isset($login))
{
	echo "error login";
	exit;
}

echo get_user($login)
?>