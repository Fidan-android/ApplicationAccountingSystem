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
                header($_SERVER["SERVER_PROTOCOL"] . " 400 - Bad Request", true, 400);
                die(json_encode(array("title" => "Uncorrectly username", 
                        "message" => "Логин может состоять только из буквы, 
                                        цифр и символа подчеркивания.", 
                                "code" => "001")));
            }
            
            if (!preg_match('/(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%^&*]{6,}', 
                                trim($data['password']))) {
                header($_SERVER["SERVER_PROTOCOL"] . " 400 - Bad Request", true, 400);
                die(json_encode(array("title" => "Uncorrectly password", 
                        "message" => "Пароль должен содержать не менее 6 символов: 
                                            хотя бы одно число, хотя бы один символ заглавной и 
                                            прописной латинской буквы, один из специальных символов.", 
                                "code" => "002")));
            }

            if (!preg_match('/(?:\+|\d)[\d\-\(\) ]{9,}\d/g', trim($data['phone']))){
                header($_SERVER["SERVER_PROTOCOL"] . " 400 - Bad Request", true, 400);
                die(json_encode(array("title" => "Uncorrectly phone", 
                        "message" => "Телефон должен быть стандартного вида.", 
                                "code" => "003")));
            }

            include_once("../database/settings.php");
            include_once("../config/aas-config.php");

            $db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
            $username = trim($data['login']);
            $password = trim($data['password']);
            $firstname = trim($data['firstname']);
            $middlename = trim($date['middlename']);
            $phone = trim($data['phone']);
            
            $response = $db->signUp(DB_PREFIX, $username, password_hash($password, PASSWORD_BCRYPT), $firstname, $middlename, $phone);

            switch($response) {
                case "success": {
                    die(json_encode(array("title" => "Account created",
                            "message" => "Аккаунт успешно создан. Пройдите аутентификацию")));
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
            die(json_encode(array("message" => "Один или несколько полей запроса отсутствуют.")));
        }
    }