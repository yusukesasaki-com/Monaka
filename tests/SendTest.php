<?php

use PHPUnit\Framework\TestCase;

require_once(__DIR__ . "/../Monaka/config/config-sample.php");

class FakeMailer extends Monaka\Mailer {
  public $calls = array();

  public function send(
    array $to,
    string $fromAddress,
    string $fromName,
    string $subject,
    string $body,
    array $attachments = array(),
    ?string $replyToAddress = null,
    string $replyToName = ''
  ) {
    $this->calls[] = array(
      "to" => $to,
      "fromAddress" => $fromAddress,
      "fromName" => $fromName,
      "subject" => $subject,
      "body" => $body,
      "attachments" => $attachments,
      "replyToAddress" => $replyToAddress,
      "replyToName" => $replyToName,
    );
    return true;
  }
}

class SendTest extends TestCase {

  public $adminMail;
  public $adminArray = array();
  public $adminName;
  public $returnMailTitle;
  public $returnMailHeader;
  public $returnMailFooter;
  public $server = array();
  public $fileContents;
  public $mailer;
  public $obj;

  public function setUp(): void {
    // PHPUnit 11 no longer auto-restores superglobals between tests, so reset explicitly.
    $_POST = array();
    $_FILES = array();
    $_SESSION = array();
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
    $this->fileContents = file_get_contents(__DIR__ . "/testFile/test.JPG");
    $file = array(
      "name" => "test.JPG",
      "tmp" => __DIR__ . "/testFile/test.JPG",
      "ext" => "jpg",
      "file" => chunk_split(base64_encode($this->fileContents))
    );
    $_SESSION["submitFile"]["添付ファイル1"] = array("test.JPG" => $file["file"]);
    $_SERVER = array(
      "REMOTE_ADDR" => "127.0.0.1",
      "REMOTE_HOST" => gethostbyaddr("127.0.0.1"),
      "HTTP_USER_AGENT" => "test user agent"
    );
    $this->server = $_SERVER;
    $_POST["requiredItem"]["name"] = "㈱㍉㌖";
    $_POST["requiredItem"]["mailaddress"] = "visitor@example.com";
    $_SESSION["submitContent"]["お問い合わせ内容"] = "㈱㍉㌖";
    $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16));
    $_POST['token'] = $_SESSION['token'];
    $this->mailer = new FakeMailer();
    $this->obj = new Monaka\Send($this->mailer);
  }

  public function testSend() {
    $this->obj->run($this->adminMail, $this->adminName, $this->returnMailTitle, $this->returnMailHeader, $this->returnMailFooter);

    // 送信失敗が記録されていないこと
    $this->assertNull($this->obj->adminMailError);
    $this->assertNull($this->obj->returnMailError);

    // Mailerが2回(管理者宛・自動返信)呼ばれること
    $this->assertCount(2, $this->mailer->calls);
    [$adminCall, $returnCall] = $this->mailer->calls;

    // 本文のチェック(UTF-8のまま、特殊文字の変換テーブルは廃止)
    $sendMessage = "㈱㍉㌖様より、下記内容でメールが届きました。\n";
    $sendMessage .= "\n";
    $sendMessage .= "■お問い合わせ内容\n";
    $sendMessage .= "㈱㍉㌖\n\n";
    $sendMessage .= "\n\n";
    $sendMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $sendMessage .= "[送信日時]".date("Y年m月d日(D) H時i分s秒")."\n";
    $sendMessage .= "[IPアドレス]{$this->server["REMOTE_ADDR"]}\n";
    $sendMessage .= "[ホスト]{$this->server["REMOTE_HOST"]}\n";
    $sendMessage .= "[USER_AGENT]{$this->server["HTTP_USER_AGENT"]}\n";
    $sendMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $this->assertEquals($sendMessage, $this->obj->sendMessage);
    $this->assertEquals($sendMessage, $adminCall["body"]);

    // 管理者宛メール: 宛先/送信元/返信先のチェック
    // (Send::run()にはtrim済みの単一アドレスであるthis->adminMailを渡しているため宛先は1件)
    $this->assertEquals(
      array(array("example@example.com", "admin")),
      $adminCall["to"]
    );
    $this->assertEquals("example@example.com", $adminCall["fromAddress"]);
    $this->assertEquals("admin", $adminCall["fromName"]);
    $this->assertEquals("visitor@example.com", $adminCall["replyToAddress"]);
    $this->assertEquals("㈱㍉㌖", $adminCall["replyToName"]);

    // 添付ファイルがbase64デコードされて元のバイナリと一致すること
    $this->assertCount(1, $adminCall["attachments"]);
    $this->assertEquals("test.JPG", $adminCall["attachments"][0]["filename"]);
    $this->assertEquals($this->fileContents, $adminCall["attachments"][0]["content"]);

    // リターンメールのチェック
    $returnMessage = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $returnMessage .= "【{$this->adminName}】 お問い合わせを受け付けました\n";
    $returnMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $returnMessage .= "\n";
    $returnMessage .= "㈱㍉㌖様\n";
    $returnMessage .= "\n";
    $returnMessage .= $this->returnMailHeader;
    $returnMessage .= "\n";
    $returnMessage .= "----------------------------------------------------------------------\n";
    $returnMessage .= "■お問い合わせ内容\n";
    $returnMessage .= "㈱㍉㌖\n\n";
    $returnMessage .= "----------------------------------------------------------------------\n";
    $returnMessage .= "\n";
    $returnMessage .= $this->returnMailFooter;
    $returnMessage .= "\n";
    $this->assertEquals($returnMessage, $this->obj->returnMessage);
    $this->assertEquals($returnMessage, $returnCall["body"]);

    // 自動返信メール: 宛先は問い合わせ者、送信元は管理者
    $this->assertEquals(array(array("visitor@example.com", "㈱㍉㌖")), $returnCall["to"]);
    $this->assertEquals("example@example.com", $returnCall["fromAddress"]);
    $this->assertEquals("admin", $returnCall["fromName"]);

    // セッションがリセットされること
    $this->assertEquals(array(), $_SESSION["submitContent"]);
    $this->assertEquals(array(), $_SESSION["submitFile"]);
  }

  public function testSendFailurePropagatesError() {
    $failingMailer = new class extends Monaka\Mailer {
      public function send(
        array $to,
        string $fromAddress,
        string $fromName,
        string $subject,
        string $body,
        array $attachments = array(),
        ?string $replyToAddress = null,
        string $replyToName = ''
      ) {
        return "SMTP connect() failed.";
      }
    };
    $this->obj = new Monaka\Send($failingMailer);
    $this->obj->run($this->adminMail, $this->adminName, $this->returnMailTitle, $this->returnMailHeader, $this->returnMailFooter);

    $this->assertSame("SMTP connect() failed.", $this->obj->adminMailError);
    $this->assertSame("SMTP connect() failed.", $this->obj->returnMailError);
  }

}
