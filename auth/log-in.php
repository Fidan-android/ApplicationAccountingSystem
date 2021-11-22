<?php
    
    //Все методы, которые разрешены к данному URL-адресу
    $allowedMethods = array(
        'POST'
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
        header('content-type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['login']) && isset($data['password'])) {
            
            include_once("../settings.php");
            include_once("../aas-config.php");

            $db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
            
            $token = $token = bin2hex(random_bytes(32));
            
            die(json_encode(array("token" => $token)));
        } else {
            header($_SERVER["SERVER_PROTOCOL"]." 400 - Bad Request", true, 400);
        }
    }