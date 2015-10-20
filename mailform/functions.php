<?php

function h($s){
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

function mail_check($email) {
    if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
        return true;
    } else {
        return false;
    }
}