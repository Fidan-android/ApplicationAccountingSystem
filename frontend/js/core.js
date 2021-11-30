if (sessionStorage.getItem('token')) {
    console.log("hello");
}

var data = {
    title: "",
    body: "",
    link: ""
};

var loaded = true;

var page = //Элементы, текст в которых будет меняться
{
    title: document.getElementById("title"),
    body: document.getElementById("body")
};

OnLoad();

function OnLoad() {
    var link = window.location.pathname; //Ссылка страницы без домена

    var href = link.replace("/", "/login");
    console.log("Onload:" + href);
    LinkClick(href);
}

function InitLinks() {
    links = document.getElementsByTagName("a"); //Находим все ссылки на странице

    for (var i = 0; i < links.length; i++) {
   	    //Отключаем событие по умолчанию и вызываем функцию LinkClick
        links[i].addEventListener("click", function (e) {
            e.preventDefault();
            LinkClick(e.target.getAttribute("href"));  
            return false;
   	    });
    }
}

function LinkClick(href) {
    var props = href.split("/"); //Получаем параметры из ссылки. 1 - раздел, 2 - идентификатор

    switch(props[1]) {
        case "login": {
            SendRequest("?page=login", href); //Отправляем запрос на сервер
   		    break;
        }

   	    case "registration": {
   		    SendRequest("?page=registration", href);
   		    break;
        }
    }
}

function SendRequest(query, link) {
    //Создаём объект для отправки запроса
    var xhr = new XMLHttpRequest(); 

    xhr.open("GET", "/backend/core.php" + query, true); //Открываем соединение

    xhr.onreadystatechange = function() {
        if (xhr.readyState != 4) return; //Если это не тот ответ, который нам нужен, ничего не делаем

        //Иначе говорим, что сайт загрузился
        loaded = true;

        if (xhr.status == 200) {
            GetData(JSON.parse(xhr.responseText), link);
        } else {
            alert("Loading error! Try again later.");
            console.log(xhr.status + ": " + xhr.statusText);
        }
    }

    loaded = false; //Говорим, что идёт загрузка

    //Устанавливаем таймер, который покажет сообщение о загрузке, если она не завершится через 2 секунды
    setTimeout(ShowLoading, 2000);
    xhr.send(); //Отправляем запрос
}

function GetData(response, link) {
    data = {
        title: response.title,
        html: response.body,
        link: link
    };

    UpdatePage(); //Обновляем контент на странице
}

function ShowLoading() {
    if(!loaded) {
        page.body.innerHTML = "Loading...";
    }
}

function UpdatePage() {
    page.title.innerText = data.title;
    page.body.innerHTML = data.html;

    document.title = data.title;
    window.history.pushState(data.body, data.title, "/" + data.link); //Меняем ссылку

    InitLinks(); //Инициализируем новые ссылки
}