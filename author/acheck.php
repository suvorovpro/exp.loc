<?php
/*
=====================================================
 Файл: check.php
-----------------------------------------------------
 Назначение: проверка логина партнера
=====================================================
*/

$link = mysqli_connect("localhost", "root", "", "exploit");
if (mysqli_connect_errno()) {
    printf("Не удалось подключиться: %s\n", mysqli_connect_error());
    exit();
}
//подключение к бд
//define('IS_LOGIN', 'no');

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {   
    $query =  mysqli_query($link,"SELECT * FROM users WHERE user_id = '".intval($_COOKIE['id'])."' LIMIT 1");
    $userdata = mysqli_fetch_assoc($query);
	$hash2 = md5($_SERVER['HTTP_USER_AGENT'].$userdata['user_hash']);
    if(($hash2 !== $_COOKIE['hash']) or ($userdata['user_id'] !== $_COOKIE['id'])   ) {
       
		
		/* setcookie("id", "", time() - 3600*24*30*12, "/");
        setcookie("hash", "", time() - 3600*24*30*12, "/"); */
		setcookie("id", "");
        setcookie("hash", "");
        print "Хм, что-то не получилось";
		define('IS_LOGIN', 'no');
		
    }
    else
    {
        //print "Привет, ".$userdata['user_login'].". Всё работает!";
		define('IS_LOGIN', 'yes');
		
    }
}

?>