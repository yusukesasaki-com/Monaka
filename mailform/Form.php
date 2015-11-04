<?php

class Form{
    
    public function __constract(){
        
    }
    
    public function create(){
        echo "<form action=\"./mailform/confirmation.php\" method=\"post\" enctype=\"multipart/form-data\">\n";
    }
    
    public function end(){
        echo "<div class=\"submit_area\">\n";
        echo "<input type=\"submit\" value=\"確認\">\n";
        echo "</div>\n";
        echo "</form>\n";
    }
    
    public function inputName($name){
        echo "<input type=\"text\" name=\"{$name}[value]\">\n";
        echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"名前\">\n";
    }
    
    public function inputMail($name){
        echo "<input type=\"text\" name=\"{$name}[value]\">\n";
        echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"メール\">\n";
    }
    
    public function inputText($name, $params = null){
        echo "<input type=\"text\" name=\"{$name}[value]\">\n";
        echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"{$params}\">\n";
    }
    
    public function select($name, $params1 = array(), $params2 = null){
        echo "<select name=\"{$name}[value]\">";
        foreach($params1 as $key => $value){
            if($value !== "noValue"){
                echo "<option value=\"{$value}\">{$value}</option>";
            }else{
                echo "<option value=\"\">{$key}</option>";
            }
        }
        echo "</select>";
        echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"{$params2}\">\n";
    }
    
    public function inputRadio($name, $params1 = array(), $params2 = null){
        $checked = "checked";
        echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"{$params2}\">\n";
        foreach($params1 as $key => $value){
            if($value === "text"){
                echo "<label><input type=\"radio\" name=\"{$name}[value]\" value=\"{$key}\" {$checked}> {$key}</label>　\n";
                echo "<input type=\"text\" name=\"{$name}-{$key}[value]\">　\n";
                echo "<input type=\"hidden\" name=\"{$name}-{$key}[params]\">\n";
            }else{
                echo "<label><input type=\"radio\" name=\"{$name}[value]\" value=\"{$value}\" {$checked}> {$value}</label>　\n";
            }
            $checked = "";
        }
    }
    
    public function inputRadioBR($name, $params1 = array(), $params2 = null){
        $checked = "checked";
        echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"{$params2}\">\n";
        foreach($params1 as $key => $value){
            if($value === "text"){
                echo "<label><input type=\"radio\" name=\"{$name}[value]\" value=\"{$key}\" {$checked}> {$key}</label>　\n";
                echo "<input type=\"text\" name=\"{$name}-{$key}[value]\">　\n";
                echo "<input type=\"hidden\" name=\"{$name}-{$key}[params]\">\n";
            }else{
                echo "<label><input type=\"radio\" name=\"{$name}[value]\" value=\"{$value}\" {$checked}> {$value}</label>　\n";
            }
            echo "<br>\n";
            $checked = "";
        }
    }
    
    public function inputCheckbox($name, $params1 = array(), $params2 = null){
        echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"{$params2}\">\n";
        foreach($params1 as $key => $value){
            if($value === "text"){
                echo "<label><input type=\"checkbox\" name=\"{$name}[value][]\" value=\"{$key}\"> {$key}</label>　\n";
                echo "<input type=\"text\" name=\"{$name}-{$key}[value]\">\n";
                echo "<input type=\"hidden\" name=\"{$name}-{$key}[params]\">\n";
            }else{
                echo "<label><input type=\"checkbox\" name=\"{$name}[value][]\" value=\"{$value}\"> {$value}</label>　\n";
            }
        }
    }
    
    public function inputCheckboxBR($name, $params1 = array(), $params2 = null){
        echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"{$params2}\">\n";
        foreach($params1 as $key => $value){
            if($value === "text"){
                echo "<label><input type=\"checkbox\" name=\"{$name}[value][]\" value=\"{$key}\"> {$key}</label>　\n";
                echo "<input type=\"text\" name=\"{$name}-{$key}[value]\">\n";
                echo "<input type=\"hidden\" name=\"{$name}-{$key}[params]\">\n";
            }else{
                echo "<label><input type=\"checkbox\" name=\"{$name}[value][]\" value=\"{$value}\"> {$value}</label>　\n";
            }
            echo "<br>\n";
        }
    }
    
    public function inputFile($name){
        echo "<input type=\"file\" name=\"{$name}\"><br>";
    }
    
    public function textarea($name, $params){
        echo "<textarea name=\"{$name}[value]\"></textarea>\n";
        echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"{$params}\">";
    }
}