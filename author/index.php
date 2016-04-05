<?php
/*
=====================================================
 Файл: index.php
-----------------------------------------------------
 Назначение: страница аторизации админа
=====================================================
*/
# Функция для генерации случайной строки
function generateCode($length=6) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;  
    while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];  
    }
    return $code;
}

//подключаем дополнительные модули

include ('acheck.php'); // проверка на логин
if(IS_LOGIN=="yes") {
header("Location: catalog.php"); exit();
}
//подключение к бд


if(isset($_POST['login'])) {
    # Вытаскиваем из БД запись, у которой логин равняеться введенному
    $log = trim($_POST['login']); 
    $log = mysqli_real_escape_string($link,$log);
    $log = htmlspecialchars($log);
    $query = mysqli_query($link,"SELECT user_id, user_password FROM users WHERE user_login='".$log."' LIMIT 1");
    $data = mysqli_fetch_assoc($query);
    
    # Сравниваем пароли
    if($data['user_password'] === md5(md5($_POST['password'])))
    {
        # Генерируем случайное число и шифруем его
        $hash = md5(generateCode(10));
        $hash2 = md5($_SERVER['HTTP_USER_AGENT'].$hash);
        
        
        # Записываем в БД новый хеш авторизации и IP
        mysqli_query($link,"UPDATE users SET user_hash='".$hash."' WHERE user_id='".$data['user_id']."'");
        
        # Ставим куки
        setcookie("id", $data['user_id'], time()+60*60*24*30);
        setcookie("hash", $hash2, time()+60*60*24*30);
        
        # Переадресовываем браузер на страницу проверки нашего скрипта
        header("Location: catalog.php"); exit();
    }
    else
    {
        $err = "Вы ввели неправильный логин или пароль";
		
    }
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    

    <title>Exploits Catalog</title>

    <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/signin.css" rel="stylesheet">

   

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">

      <form class="form-signin" role="form" method="POST">
        <h2 class="form-signin-heading">Пожалуйста войдите</h2>
        <input name="login" type="text" class="form-control" placeholder="Логин" required autofocus><br>
        <input name="password" type="password" class="form-control" placeholder="Пароль" required>
		<?php echo "<span class=\"help-block \"><b>$err</b></span>"; ?>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>
      </form>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  </body>
</html>
