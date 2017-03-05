<?php

require_once(__DIR__ . "/../Monaka/config/config-sample.php");

class SendTest extends PHPUnit_Framework_TestCase {

  public $adminMail;
  public $adminArray = array();
  public $adminName;
  public $returnMailTitle;
  public $returnMailHeader;
  public $returnMailFooter;
  public $server = array();
  public $session = array();

  public function setUp() {
    $adminMail = "example@example.com, example2@example.com";
    $this->adminArray = explode(",", $adminMail);
    $this->adminMail = trim($this->adminArray[0]);
    $this->adminName = "admin";
    $this->returnMailTitle = "お問い合わせを受け付けました";
    $this->returnMailHeader = <<<EOD
お問い合わせフォームよりお問い合わせをいただきありがとうございます。

お問い合わせ内容を確認の上、ご返信先メールアドレスへ回答いたしますので、
しばらくお待ちくださいますようお願いいたします。
なお、お問い合わせから48時間経過しましても回答がない場合、
サポートにてお問い合わせが受信できていない可能性がございます。
大変お手数ですが、「{$this->adminMail}」まで
再度お問い合わせくださいますようお願いいたします。
EOD
;
    $this->returnMailFooter = <<<EOD
ありがとうございます。
EOD
;
    $fp = fopen(__DIR__ . "/testFile/test.jpg", "r");
    $contents = fread($fp, filesize(__DIR__ . "/testFile/test.jpg"));
    fclose($fp);
    $file = array(
      "name" => "test.jpg",
      "tmp" => __DIR__ . "/testFile/test.jpg",
      "ext" => "jpg",
      "file" => chunk_split(base64_encode($contents))
    );
    $_SESSION["submitFile"]["添付ファイル1"] = $file;
    $_SESSION["submitFile"]["添付ファイル2"] = $file;
    $this->session["submitFile"]["添付ファイル1"] = $file;
    $this->session["submitFile"]["添付ファイル2"] = $file;
    $_SERVER = array(
      "REMOTE_ADDR" => "127.0.0.1",
      "REMOTE_HOST" => gethostbyaddr("127.0.0.1"),
      "HTTP_USER_AGENT" => "test user agent"
    );
    $this->server = $_SERVER;
    $_POST["requiredItem"]["name"] = "㈱㍉㌖";
    $_POST["requiredItem"]["mailaddress"] = "example@example.com";
    $_SESSION["submitContent"]["お問い合わせ内容"] = "㈱㍉㌖";
    $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16));
    $_POST['token'] = $_SESSION['token'];
    $this->obj = new Monaka\Send();
  }

  public function testSend() {
    $this->obj->run($this->adminMail, $this->adminName, $this->returnMailTitle, $this->returnMailHeader, $this->returnMailFooter);

    // 本文のチェック
    $sendMessage = "㈱ミリキロメートル様より、下記内容でメールが届きました。\n";
    $sendMessage .= "\n";
    $sendMessage .= "■お問い合わせ内容\n";
    $sendMessage .= "㈱ミリキロメートル\n\n";
    $sendMessage .= "\n\n";
    $sendMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $sendMessage .= "[送信日時]".date("Y年m月d日(D) H時i分s秒")."\n";
    $sendMessage .= "[IPアドレス]{$this->server["REMOTE_ADDR"]}\n";
    $sendMessage .= "[ホスト]{$this->server["REMOTE_HOST"]}\n";
    $sendMessage .= "[USER_AGENT]{$this->server["HTTP_USER_AGENT"]}\n";
    $sendMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $sendMessage = mb_convert_encoding($sendMessage, "ISO-2022-JP-MS","UTF-8");
    $tmpMessage = $sendMessage;
    $sendMessage = "--{$this->obj->boundary}\n";
    $sendMessage .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
    $sendMessage .= "Content-Transfer-Encoding: 7bit\n\n";
    $sendMessage .= $tmpMessage."\n";
    foreach ($this->session["submitFile"] as $key => $value) {
      foreach ($value as $key2 => $value2) {
        $name = $key2;
        $f_encoded = $value2;
        $sendMessage .= "\n";
        $sendMessage .= "--{$this->obj->boundary}\n";
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
    $sendMessage .= "--{$this->obj->boundary}--\n";
    $sendMessage = str_replace("\r", "", $sendMessage);
    $this->assertEquals($this->obj->sendMessage, str_replace("\r", "", $sendMessage));

    // リターンメールのチェック
    $returnMessage = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $returnMessage .= "【{$this->adminName}】 お問い合わせを受け付けました\n";
    $returnMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $returnMessage .= "\n";
    $returnMessage .= "㈱ミリキロメートル様\n";
    $returnMessage .= "\n";
    $returnMessage .= $this->returnMailHeader;
    $returnMessage .= "\n";
    $returnMessage .= "----------------------------------------------------------------------\n";
    $returnMessage .= "■お問い合わせ内容\n";
    $returnMessage .= "㈱ミリキロメートル\n\n";
    $returnMessage .= "----------------------------------------------------------------------\n";
    $returnMessage .= "\n";
    $returnMessage .= $this->returnMailFooter;
    $returnMessage .= "\n";
    $returnMessage = mb_convert_encoding($returnMessage, "ISO-2022-JP-MS","UTF-8");
    $this->assertEquals($this->obj->returnMessage, $returnMessage);
  }

}
