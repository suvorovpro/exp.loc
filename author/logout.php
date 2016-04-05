<?php

		setcookie("id", "", 1);
        setcookie("hash", "", 1);
        define('IS_LOGIN', 'no');
		header("Location: ../"); exit();

?>