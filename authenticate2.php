<?php // authenticate.php
 require_once 'login.php'; //require the file
 $connection = new mysqli($hn, $un, $pw, $db);
 if ($connection->connect_error) die($connection->connect_error);
 if (isset($_SERVER['PHP_AUTH_USER']) &&
 isset($_SERVER['PHP_AUTH_PW']))
 {
    $un_temp = sanitizeMySQL($connection,$_SERVER['PHP_AUTH_USER']) ;
    $pw_temp = sanitizeMySQL($connection,$_SERVER['PHP_AUTH_PW']) ;
    $query = "SELECT * FROM use3 WHERE username='$un_temp'";
    $result = $connection->query($query) ;
    if (!$result) die($connection->error) ;
    elseif ($result->num_rows)
         {
              $row = $result->fetch_array(MYSQLI_NUM);
              $result->close();
              $salt1 = "qm&h*";
              $salt2 = "pg!@";
              $token = hash('ripemd128', "$salt1$pw_temp$salt2");
              if ($token == $row[3]) 
			        {
						session_start();
						$_SESSION['username']=$un_temp;
						$_SESSION['password']=$pw_temp;
						$_SESSION['forename']=$row[0];
						$_SESSION['surname']=$row[1];
				        echo "$row[0] $row[1] :Привет, $row[0] , теперь вы 
						                        зарегистрированы под 
												именем '$row[2]'";
					die("<p><a href='continue.php'>Щелкните здесь для продолжения </a></p>");
					}
              else die("Неверная комбинация имя пользователя — пароль") ;
          }
     else die("Неверная комбинация имя пользователя — пароль") ;
 }
 else
 {
 header('WWW-Authenticate: Basic realm="Restricted Section"');
 header(' HTTP/1.0 401 Unauthorized');
 die ("Пожалуйста, введите имя пользователя и пароль");
 }
 $connection->close();
function sanitizeString($var)
{
	$var=stripslashes($var);
	$var=htmlentities($var);
	$var=strip_tags($var);
	return $var;
}
function sanitizeMySQL($connection, $var)
{
	$var=$connection->real_escape_string($var);
	$var=sanitizeString($var);
	return $var;
}
?>
