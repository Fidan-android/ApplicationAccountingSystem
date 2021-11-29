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
        
    }