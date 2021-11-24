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
        
        if (isset($data['login']) && isset($data['password']) && isset($data['firstname']) 
                    && isset($data['middlename']) && isset($data['phone'])) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', trim($data["login"]))) {
                header($_SERVER["SERVER_PROTOCOL"]." 400 - Bad Request", true, 400);
                die(json_encode(array("title" => "Uncorrectly username", 
                        "message" => "Username can only contain letters, numbers and underscores.", 
                                "code" => "001")));
            }
            
            if (!preg_match('/(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%^&*]{6,}/g', trim($password))) {
                header($_SERVER["SERVER_PROTOCOL"]." 400 - Bad Request", true, 400);
                die(json_encode(array("title" => "Uncorrectly username", 
                        "message" => "Username can only contain letters, numbers and underscores.", 
                                "code" => "002")));
            }

            include_once("../database/settings.php");
            include_once("../config/aas-config.php");

            $db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
            $username = trim($data['login']);
            $password = trim($data['password']);
            
            $response = $db->signIn(DB_PREFIX, $username, $password);

            switch($response) {
                case "success": {
                    $token = bin2hex(random_bytes(32));
                    $db->saveToken($username, $token);
                    die(json_encode(array("token" => $token)));
                    break;
                }
                
                case "error": {
                    header("401 - Unauthorized", true, 401);
                    die(json_encode(array("message" => "Неверны логин или пароль", "code" => "010")));
                    break;
                }
                
                default: {
                    header($_SERVER["SERVER_PROTOCOL"] . "500 - Internal Server Error", true, 500);
                    die(json_encode(array("message" => $response)));
                    break;
                }
            }
            
        } else {
            header($_SERVER["SERVER_PROTOCOL"]." 400 - Bad Request", true, 400);
        }
    }