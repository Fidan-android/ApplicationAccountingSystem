<?php
    
    //Все методы, которые разрешены к данному URL-адресу
    $allowedMethods = array(
        'GET'
    );

    //Текущий метод к данному URL-адресу
    $requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);


    //Проверка текущего метода с допустимыми методами
    if(!in_array($requestMethod, $allowedMethods)){
        //Отправка ошибки 405 Метод не поддерживается, с заголовком Разрешенные методы
        header($_SERVER["SERVER_PROTOCOL"]." 405 Method Not Allowed", true, 405);
        header("Allow:" . json_encode($allowedMethods));
        exit;
    } else {
        if (!isset($_GET['page'])) {
            header($_SERVER["SERVER_PROTOCOL"]." 400 - Bad Request", true, 400);
        }

        include_once("./page.php");
        $page_name = $_GET['page'];

        switch($page_name) {
            case "login": {
                $login_page = new Page($page_name, file_get_contents("../frontend/template/login.html"));
                die(json_encode($login_page));
                break;
            }

            default: {
                break;
            }
        }
    }