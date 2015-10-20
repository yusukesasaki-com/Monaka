<?php

function h($s){
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

function mailCheck($email) {
    if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
        return true;
    } else {
        return false;
    }
}

function setToken(){
    $_SESSION['token'] = sha1(uniqid(mt_rand(), true));
}

function checkToken(){
    if(empty($_POST['token']) || ($_SESSION['token'] != $_POST['token'])){
        echo "不正な送信です。";
        exit;
    }
}