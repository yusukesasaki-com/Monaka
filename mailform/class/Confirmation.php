<?php

class Confirmation{
    
    public $adminMail;
    public $requiredItem = array();
    public $submitContent = array();
    public $err = array();
    public $nameCheck = false;
    public $mailCheck = false;
    public $fileData = array();
    public $submitFile = array();
    public $seriousError;
    
    public function __construct($adminMail){
        $this->adminMail = $adminMail;
    }
    
    public function postCheck($post){
        foreach($post as $key => $values){
    
            // $params = explode(",", $values["params"]); /* 今後in_arrayと組み合わせて使うかも?*/

            // 配列(checkbox)を変数に変換
            if(is_array($values["value"])){
                $values["value"] = implode("、", $values["value"]);
            }

            // 名前チェック
            if(strpos($values["params"], "名前") !== false){
                $this->nameCheck = true;
                if(empty($values["value"])){
                    $this->err[$key] = "必須項目です。";
                }
                $this->submitContent[$key] = $values["value"];
                $this->requiredItem["name"] = $values["value"];
                continue;
            }

            // メールチェック
            if(strpos($values["params"], "メール") !== false){
                $this->mailCheck = true;
                if(empty($values["value"])){
                    $this->err[$key] = "必須項目です。";
                }else{

                    // メールアドレスの形式チェック
                    if(!mailCheck($values["value"])){
                        $this->err[$key] = "メールアドレスの形式が正しくありません。";
                    }
                    /* filter_varを使用する場合は下記
                    if(!filter_var($this->submitContent["mailaddress"], FILTER_VALIDATE_EMAIL)){
                        $this->err['mailaddress'] = "メールアドレスの形式が正しくありません";
                    }
                    */
                }
                $this->submitContent[$key] = $values["value"];
                $this->requiredItem["mailaddress"] = $values["value"];
                continue;
            }

            // メール再入力チェック
            if(strpos($values["params"], "再入力") !== false){
                if($this->requiredItem["mailaddress"] !== $values["value"]){
                    $this->err[$key] = "メールアドレスが一致しません。";
                    $this->submitContent[$key] = $values["value"];
                }
                continue;
            }

            // 電話番号チェック
            if(strpos($values["params"], "電話番号") !== false){
                if(!empty($values["value"])){
                    if(!telCheck($values["value"])){
                        $this->err[$key] = "電話番号を正しく入力してください。";
                        $this->submitContent[$key] = $values["value"];
                    continue;
                    }
                }
            }

            // 郵便番号チェック
            if(strpos($values["params"], "郵便番号") !== false){
                if(!empty($values["value"])){
                    if(!zipCheck($values["value"])){
                        $this->err[$key] = "郵便番号を正しく入力してください。";
                        $this->submitContent[$key] = $values["value"];
                    continue;
                    }
                }
            }

            // 必須チェック
            if(strpos($values["params"], "必須") !== false){
                if(empty($values["value"])){
                    $this->err[$key] = "必須項目です。";
                }
                $this->submitContent[$key] = $values["value"];
                continue;
            }

            $this->submitContent[$key] = $values["value"];

            }
    }
    
    public function filesCheck($files){
        if(!empty($files)){
            foreach($files as $key => $value){
                if(!empty($value["tmp_name"])){
                    $this->fileData["tmp"] = $value["tmp_name"];
                    $this->fileData["name"] = $value["name"];
                    $this->fileData["size"] = $value["size"];
                    $this->fileData["array"] = explode(".", $this->fileData["name"]);
                    $this->fileData["nr"] = count($this->fileData["array"]);
                    $this->fileData["ext"] = $this->fileData["array"][$this->fileData["nr"] - 1];

                    if($ext_denied == 1 && !@in_array($this->fileData["ext"], $EXT_ALLOWS)){
                        $err[$key]  = "添付できないファイルです<br>\n";
                        $err[$key] .= "添付可能なファイルの種類（拡張子）は[".implode("・", $EXT_ALLOWS)."]です\n";
                        continue;
                    }

                    if($maxmemory == 1 && ($this->fileData["size"] / 1000) > $max){
                        $err[$key]  = "ファイルの容量が大きすぎます<br>\n";
                        continue;
                    }

                    $fp = fopen($this->fileData["tmp"], "r");
                    $contents = fread($fp, filesize($this->fileData["tmp"]));
                    fclose($fp);

                    $this->submitFile[$key]["name"] = $this->fileData["name"];
                    $this->submitFile[$key]["tmp"] = $this->fileData["tmp"];
                    $this->submitFile[$key]["ext"] = $this->fileData["ext"];
                    $this->submitFile[$key]["file"] = chunk_split(base64_encode($contents)); //エンコードして分割
                }
            }
        }
    }
    
    public function seriousErrorCheck(){
        if(!$this->nameCheck || !$this->mailCheck){
            $seriousError = "エラーが発生しました。<br>\n";
            $seriousError .= "再度お試しいただき、解消しない場合は、<br>\n";
            $seriousError .= "管理者【{$adminMail}】にお知らせください。";
        }
    }

}