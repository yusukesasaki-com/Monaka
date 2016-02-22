<?php

require_once(__DIR__ . "/../mailform/class/Send.php");

class SendTest extends PHPUnit_Framework_TestCase {

  public $adminMail;
  public $adminArray = array();
  public $adminName;
  public $returnMailHeader;
  public $returnMailFooter;
  public $submitFile = array();
  public $server = array();

  public function setUp() {
    $adminMail = "example@example.com, example2@example.com";
    $this->adminArray = explode(",", $adminMail);
    $this->adminMail = trim($this->adminArray[0]);
    $this->adminName = "admin";
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
    $this->submitFile = array();
    $this->submitFile["添付ファイル1"] = $file;
    $this->submitFile["添付ファイル2"] = $file;
    $this->server = array(
      "REMOTE_ADDR" => "127.0.0.1",
      "REMOTE_HOST" => "test host",
      "HTTP_USER_AGENT" => "test user agent"
    );
    $this->obj = new Send(
      $adminMail,
      $this->adminName,
      $this->returnMailHeader,
      $this->returnMailFooter,
      $this->submitFile,
      $this->server
    );
  }

  public function testValidSubstitution() {
    $post = array();
    $requireItem = array();
    $requireItem["name"] = "㈱㍉㌖";
    $requireItem["mailaddress"] = "example@example.com";
    $post["お問い合わせ内容"] = "㈱㍉㌖";
    $this->obj->substitutionRequiredItem($requireItem);
    $this->obj->substitutionSubmitContent($post);
    $this->assertEquals($this->obj->requiredItem["name"], "㈱ミリキロメートル");
    $this->assertEquals($this->obj->submitContent["お問い合わせ内容"], "㈱ミリキロメートル");

    return array($this->obj->requiredItem, $this->obj->submitContent);
  }

  /**
   * @depends testValidSubstitution
   */
  public function testAdminSend(array $obj) {
    $this->obj->requiredItem = $obj[0];
    $this->obj->submitContent = $obj[1];
    $this->obj->adminSend();

    // 送信先のチェック
    $this->assertEquals($this->obj->sendMail[0], "{$this->adminName} <{$this->adminMail[0]}>");
    $this->assertEquals($this->obj->sendMail[1], "{$this->adminName} <{$this->adminMail[1]}>");

    // タイトルのチェック
    $sendTitle = "㈱ミリキロメートル様よりお問い合わせ";
    $sendTitle = mb_encode_mimeheader($sendTitle, "ISO-2022-JP-MS","UTF-8");
    $this->assertEquals($this->obj->sendTitle, $sendTitle);

    // 本文のチェック
    $sendMessage = "㈱ミリキロメートル様より、下記内容でお問い合わせが届いています。\n";
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
    foreach ($this->submitFile as $key => $value) {
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
    $this->assertEquals($this->obj->sendMessage, $sendMessage);

    // ヘッダーのチェック
    $sendHeaders = "X-Mailer: PHP5\r\n";
    $sendHeaders = "MIME-Version: 1.0\r\n";
    $sendHeaders .= "From: ".mb_encode_mimeheader("㈱ミリキロメートル", "ISO-2022-JP-MS","UTF-8") ." <example@example.com> \r\n";
    $sendHeaders .= "Content-Transfer-Encoding: 7bit\r\n";
    $sendHeaders .= "Content-type: multipart/mixed; boundary=\"{$this->obj->boundary}\" \r\n";
    $this->assertEquals($this->obj->sendHeaders, $sendHeaders);
  }

  /**
   * @depends testValidSubstitution
   */
  public function testReturnSend(array $obj) {
    $this->obj->requiredItem = $obj[0];
    $this->obj->submitContent = $obj[1];
    $this->obj->returnSend();

    // 送信先のチェック
    $returnMail = mb_encode_mimeheader("㈱ミリキロメートル", "ISO-2022-JP-MS","UTF-8") ." <example@example.com>";
    $this->assertEquals($this->obj->returnMail, $returnMail);

    // タイトルのチェック
    $returnTitle = "【{$this->adminName}】 お問い合わせを受け付けました";
    $returnTitle = mb_encode_mimeheader($returnTitle, "ISO-2022-JP-MS","UTF-8");
    $this->assertEquals($this->obj->returnTitle, $returnTitle);

    // メッセージのチェック
    $returnMessage = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $returnMessage .= "【{$this->adminName}】 お問い合わせを受け付けました\n";
    $returnMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $returnMessage .= "\n";
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

    //ヘッダーのチェック
    $returnHeaders = "MIME-Version: 1.0\r\n";
    $returnHeaders .= "Content-type: text/plain; charset=ISO-2022-JP\r\n";
    $returnHeaders .= "From: ".mb_encode_mimeheader($this->adminName, "ISO-2022-JP-MS","UTF-8") ." <{$this->adminMail}> \r\n";
    $this->assertEquals($this->obj->returnHeaders, $returnHeaders);
  }

}
