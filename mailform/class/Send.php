<?php

class Send{
    
    public $adminMail;
    public $adminName;
    public $returnMailHeader;
    public $returnMailFooter;
    public $requiredItem = array();
    public $submitContent = array();
    public $submitFile = array();
    public $boundary;
    
    public function __construct($adminMail,$adminName,$returnMailHeader,$returnMailFooter,$submitFile){
        $this->adminMail = $adminMail;
        $this->adminName = $adminName;
        $this->returnMailHeader = $returnMailHeader;
        $this->returnMailFooter = $returnMailFooter;
        $this->submitFile = $submitFile;
    }
    
    public function substitutionRequiredItem($post){
        foreach($post as $key => $value){
            $this->requiredItem[$key] = replaceText($value);
        }
    }

    public function substitutionSubmitContent($post){
        foreach($post as $key => $value){
            $this->submitContent[$key] = replaceText($value);
        }
    }
    
    public function characterSetting(){
        // メールの言語・文字コードの設定
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        // バウンダリー文字（パートの境界）
        $this->boundary = md5(uniqid(rand()));
    }
    
    public function adminSend(){
        // 送信先の設定
        $sendMail = mb_encode_mimeheader($this->adminName, "ISO-2022-JP-MS","UTF-8") ." <{$this->adminMail}>";

        // タイトルの設定
        $sendTitle = "{$this->requiredItem["name"]}様よりお問い合わせ";
        $sendTitle = mb_encode_mimeheader($sendTitle, "ISO-2022-JP-MS","UTF-8");

        // メッセージの設定
        $sendMessage = "{$this->requiredItem["name"]}様より、下記内容でお問い合わせが届いています。\n";
        $sendMessage .= "\n";
        foreach($this->submitContent as $key => $value){
            $sendMessage .= "■{$key}\n";
            $sendMessage .= "{$value}\n\n";
        }
        $sendMessage .= "\n\n";
        $sendMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $sendMessage .= "[送信日時]".date("Y年m月d日(D) H時i分s秒")."\n";
        $sendMessage .= "[ホスト]{$_SERVER["REMOTE_ADDR"]}\n";
        $sendMessage .= "[USER_AGENT]{$_SERVER["HTTP_USER_AGENT"]}\n";
        $sendMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $sendMessage = mb_convert_encoding($sendMessage, "ISO-2022-JP-MS","UTF-8");

        //ヘッダーの設定
        $sendHeaders = "X-Mailer: PHP5\r\n";
        $sendHeaders = "MIME-Version: 1.0\r\n";
        $sendHeaders .= "From: ".mb_encode_mimeheader($this->requiredItem["name"], "ISO-2022-JP-MS","UTF-8") ." <{$this->requiredItem["mailaddress"]}> \r\n";
        $sendHeaders .= "Content-Transfer-Encoding: 7bit\r\n";

        // 添付ファイルの設定
        if(!empty($this->submitFile)){
            $sendHeaders .= "Content-type: multipart/mixed; boundary=\"{$this->boundary}\" \r\n";

            $tmpMessage = $sendMessage;

            $sendMessage = "--{$this->boundary}\n";
            $sendMessage .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
            $sendMessage .= "Content-Transfer-Encoding: 7bit\n\n";
            $sendMessage .= $tmpMessage."\n";

            foreach($this->submitFile as $key => $value){
                foreach($value as $key2 => $value2){
                    $name = $key2;
                    $f_encoded = $value2;

                    $sendMessage .= "\n";
                    $sendMessage .= "--{$this->boundary}\n";
                    $sendMessage .= "Content-Type: application/octet-stream; ";
                    $sendMessage .= "charset=\"ISO-2022-JP\" ";
                    $sendMessage .= "name=\"".mb_encode_mimeheader($name, "ISO-2022-JP-MS","UTF-8")."\"\n";
                    $sendMessage .= "Content-Transfer-Encoding: base64\n";
                    $sendMessage .= "Content-Disposition: attachment; ";
                    $sendMessage .= "filename=\"".mb_encode_mimeheader($name, "ISO-2022-JP-MS","UTF-8")."\"\n";
                    $sendMessage .= "\n";
                    $sendMessage .= "{$f_encoded}\n";
                }
            }

            $sendMessage .= "--{$this->boundary}--\n";

        }else{
            $sendHeaders .= "Content-type: text/plain; charset=\"ISO-2022-JP\" \r\n";
        }

        // メールの送信 (宛先, 件名, 本文, 送り主(From:が必須))
        @mail($sendMail, $sendTitle, $sendMessage, $sendHeaders);
    }

    public function returnSend(){
        // 送信先の設定
        $returnMail = mb_encode_mimeheader($this->requiredItem["name"], "ISO-2022-JP-MS","UTF-8") ." <{$this->requiredItem["mailaddress"]}>";

        // タイトルの設定
        $returnTitle = "【{$this->adminName}】 お問い合わせを受け付けました";
        $returnTitle = mb_encode_mimeheader($returnTitle, "ISO-2022-JP-MS","UTF-8");

        // メッセージの設定
        $returnMessage = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $returnMessage .= "【{$this->adminName}】 お問い合わせを受け付けました\n";
        $returnMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $returnMessage .= "\n";
        $returnMessage .= "\n";
        $returnMessage .= $this->returnMailHeader;
        $returnMessage .= "\n";
        $returnMessage .= "----------------------------------------------------------------------\n";
        foreach($this->submitContent as $key => $value){
            $returnMessage .= "■{$key}\n";
            $returnMessage .= "{$value}\n\n";
        }
        $returnMessage .= "----------------------------------------------------------------------\n";
        $returnMessage .= "\n";
        $returnMessage .= $this->returnMailFooter;
        $returnMessage .= "\n";
        $returnMessage = mb_convert_encoding($returnMessage, "ISO-2022-JP-MS","UTF-8");

        //ヘッダーの設定
        $returnHeaders = "MIME-Version: 1.0\r\n";
        $returnHeaders .= "Content-type: text/plain; charset=ISO-2022-JP\r\n";
        $returnHeaders .= "From: ".mb_encode_mimeheader($this->adminName, "ISO-2022-JP-MS","UTF-8") ." <{$this->adminMail}> \r\n";

        // メールの送信 (宛先, 件名, 本文, 送り主(From:が必須))
        @mail($returnMail, $returnTitle, $returnMessage, $returnHeaders);
    }
}