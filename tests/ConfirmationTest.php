<?php

require_once(__DIR__ . "/../Monaka/config/config-sample.php");

class ConfirmationTest extends PHPUnit_Framework_TestCase {

  public $adminMail;
  public $ext_denied;
  public $EXT_ALLOWS;
  public $maxmemory;
  public $max;

  public function setUp() {
    $this->adminMail = "example@example.com";
    $this->ext_denied = 1;
    $this->EXT_ALLOWS = array("jpg", "jpeg", "gif");
    $this->maxmemory = 1;
    $this->max = 100;
    $this->obj = new Monaka\Confirmation();
  }

  /**
   * @dataProvider postValidCheckValue
   */
  public function testRun($keys, $data, $contentLength, $message) {
    $num = count($data);
    for ($i = 0; $i < $num; $i++) {
      $_POST[$keys[$i]] = $data[$i];
    }
    $this->obj->run($this->adminMail, $this->ext_denied, $this->EXT_ALLOWS, $this->maxmemory, $this->max, $contentLength);
    $this->assertEquals($this->obj->err, $message);
  }

  public function postValidCheckValue() {
    $text = "";
    for ($i = 0; $i < 45; $i++) {
      $text .= "てきすとてきすとてきすと";
    }
    $err = "長文を改行なしで入力されているようです。<br>" . PHP_EOL;
    $err .= "このまま送信すると文字化けしてしまうため、" . PHP_EOL;
    $err .= "490文字以内で改行してください。" . PHP_EOL;

    return array(
      array(
        array("お名前", "メールアドレス", "メールアドレス再入力", "電話番号", "郵便番号"),
        array(
          array("value" => "TEST", "params" => "名前"),
          array("value" => "example@example.com", "params" => "メール"),
          array("value" => "example@example.com", "params" => "再入力"),
          array("value" => "000-000-0000", "params" => "電話番号"),
          array("value" => "000-0000", "params" => "郵便番号"),
        ),
        1000, array()
      ),
      array(
        array("お名前", "メールアドレス", "メールアドレス再入力", "電話番号", "郵便番号"),
        array(
          array("value" => "TEST", "params" => "名前"),
          array("value" => "example@example.com", "params" => "メール"),
          array("value" => "exampl@example.com", "params" => "再入力"),
          array("value" => "000-000-000", "params" => "電話番号"),
          array("value" => "000-000", "params" => "郵便番号"),
        ),
        1000,
        array(
          "メールアドレス再入力" => "メールアドレスが一致しません。",
          "郵便番号" => "郵便番号を正しく入力してください。",
          "電話番号" => "電話番号を正しく入力してください。"
        )
      ),
      array(
        array("お名前", "メールアドレス", "コメント1", "コメント2"),
        array(
          array("value" => "TEST", "params" => "名前"),
          array("value" => "example@example.com", "params" => "メール"),
          array("value" => "", "params" => "必須"),
          array("value" => $text, "params" => ""),
        ),
        1000,
        array(
          "コメント1" => "必須項目です。",
          "コメント2" => $err
        )
      )
    );
  }

  /**
   * @dataProvider postSeriousWrongCheckValue
   */
  public function testSeriousWrongPost($keys, $data, $contentLength, $message) {
    $num = count($data);
    for ($i = 0; $i < $num; $i++) {
      $_POST[$keys[$i]] = $data[$i];
    }
    $this->obj->run($this->adminMail, $this->ext_denied, $this->EXT_ALLOWS, $this->maxmemory, $this->max, $contentLength);
    $this->assertEquals($this->obj->seriousError, $message);
  }

  public function postSeriousWrongCheckValue() {
    $err = "エラーが発生しました。<br>\n";
    $err .= "再度お試しいただき、解消しない場合は、<br>\n";
    $err .= "管理者【example@example.com】にお知らせください。";

    $err2 = "ファイルサイズの総量が大きすぎる可能性があります。<br>\n";
    $err2 .= "再度お試しいただき、解消しない場合は、<br>\n";
    $err2 .= "管理者【example@example.com】にお知らせください。";

    return array(
      array(
        array("お名前"),
        array(
          array("value" => "TEST", "params" => "名前"),
        ),
        1000, $err
      ),
      array(
        array("メールアドレス"),
        array(
          array("value" => "example@example.com", "params" => "メールアドレス"),
        ),
        1000, $err
      ),
      array(
        array("お名前", "メールアドレス"),
        array(
          array("value" => "TEST", "params" => "名前"),
          array("value" => "example@example.com", "params" => "メールアドレス"),
        ),
        10000000, $err2
      )
    );
  }

  /**
   * @dataProvider postTestValidFile
   */
  public function testValidFile($keys, $data, $contentLength, $file, $message) {
    $num = count($data);
    $_FILES = $file;
    $this->obj->run($this->adminMail, $this->ext_denied, $this->EXT_ALLOWS, $this->maxmemory, $this->max, $contentLength);
    $this->assertEquals($this->obj->err, $message);
  }

  public function postTestValidFile() {
    $file1["添付ファイル"] = array(
      "name" => "test.JPG",
      "type" => "image/jpeg",
      "size" => 19100,
      "tmp_name" => __DIR__ . "/testFile/test.JPG",
      "error" => 0
    );

    $file2["添付ファイル"] = array(
      "name" => "test2.jpg",
      "type" => "image/jpeg",
      "size" => 247872,
      "tmp_name" => __DIR__ . "/testFile/test2.jpg",
      "error" => 0
    );
    $err = "ファイルの容量が大きすぎます<br>\n";

    $file3["添付ファイル"] = array(
      "name" => "test.pdf",
      "type" => "application/pdf",
      "size" => 38308,
      "tmp_name" => __DIR__ . "/testFile/test.pdf",
      "error" => 0
    );
    $err2 = "添付できないファイルです<br>\n";
    $err2 .= "添付可能なファイルの種類（拡張子）は[jpg・jpeg・gif]です\n";

    return array(
      array(
        array("お名前", "メールアドレス"),
        array(
          array("value" => "TEST", "params" => "名前"),
          array("value" => "example@example.com", "params" => "メール"),
        ),
        1000, $file1, array()
      ),
      array(
        array("お名前", "メールアドレス"),
        array(
          array("value" => "TEST", "params" => "名前"),
          array("value" => "example@example.com", "params" => "メール"),
        ),
        1000, $file2, array("添付ファイル" => $err)
      ),
      array(
        array("お名前", "メールアドレス"),
        array(
          array("value" => "TEST", "params" => "名前"),
          array("value" => "example@example.com", "params" => "メール"),
        ),
        1000, $file3, array("添付ファイル" => $err2)
      )
    );
  }

}
