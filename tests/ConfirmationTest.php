<?php

require_once(__DIR__ . "/../mailform/class/Confirmation.php");

class ConfirmationTest extends PHPUnit_Framework_TestCase {
  
  public $ext_denied;
  public $ext_allows;
  public $maxmemory;
  public $max;
  
  public function setUp() {
    $this->obj = new Confirmation("example@example.com");
    // 拡張子制限（0=しない・1=する）
    $this->ext_denied = 1;
    // 許可する拡張子リスト
    $ext_allow1 = "jpg";
    $ext_allow2 = "jpeg";
    $ext_allow3 = "gif";
    // 配列に格納しておく
    $this->ext_allows = array($ext_allow1, $ext_allow2, $ext_allow3);
    // アップロード容量制限（0=しない・1=する）
    $this->maxmemory = 1;
    // 最大容量（KB）
    $this->max = 100;
  }
  
  /**
   * @dataProvider postValidCheckValue
   */
  public function testValidPost($title, $params, $value) {
    $post = array();
    $post[$title] = array("params" => $params, "value" => $value);
    $this->obj->postCheck($post);
    $this->assertEmpty($this->obj->err);
  }
  
  public function postValidCheckValue() {
    return array(
      array("お名前", "名前", "名前"),
      array("メールアドレス", "メール", "example@example.com"),
      array("電話番号", "電話番号", "000-000-0000"),
      array("郵便番号", "郵便番号", "000-0000")
    );
  }
  
  /**
   * @dataProvider postWrongCheckValue
   */
  public function testWrongPost($title, $params, $value, $errMessage) {
    $post = array();
    $post[$title] = array("params" => $params, "value" => $value);
    $this->obj->postCheck($post);
    $this->assertEquals($this->obj->err[$title], $errMessage);
  }
  
  public function postWrongCheckValue() {
    return array(
      array("お名前", "名前", "", "必須項目です。"),
      array("メールアドレス", "メール", "", "必須項目です。"),
      array("メールアドレス", "メール", "example@", "メールアドレスの形式が正しくありません。"),
      array("電話番号", "電話番号", "000-000-", "電話番号を正しく入力してください。"),
      array("郵便番号", "郵便番号", "000-", "郵便番号を正しく入力してください。"),
      array("必須項目", "必須項目", "", "必須項目です。")
    );
  }
  
  /**
   * @dataProvider postSeriousWrongCheckValue
   */
  public function testSeriousWrongPost($checkingVariable) {
    $post = array();
    $seriousError = "エラーが発生しました。<br>\n";
    $seriousError .= "再度お試しいただき、解消しない場合は、<br>\n";
    $seriousError .= "管理者【{$this->obj->adminMail}】にお知らせください。";
    $post[] = array("params" => "", "value" => "");
    $this->obj->postCheck($post);
    $this->assertFalse($this->obj->$checkingVariable);
    $this->obj->seriousErrorCheck();
    $this->assertEquals($this->obj->seriousError, $seriousError);
  }
  
  public function postSeriousWrongCheckValue() {
    return array(
      array("nameCheck"),
      array("mailCheck"),
    );
  }
  
  public function testMailReEnter() {
    $this->obj->requiredItem["mailaddress"] = "example@example.com";
    $post["メールアドレス再入力"] = array("params" => "再入力", "value" => "example@example.com");
    $this->obj->postCheck($post);
    $this->assertEmpty($this->obj->err);
  }
  
  public function testWrongMailReEnter() {
    $this->obj->requiredItem["mailaddress"] = "example@example.com";
    $post["メールアドレス再入力"] = array("params" => "再入力", "value" => "exampl@example.com");
    $this->obj->postCheck($post);
    $this->assertEquals($this->obj->err["メールアドレス再入力"], "メールアドレスが一致しません。");
  }
  
  public function testValidFile() {
    $files = array();
    $files["添付ファイル"] = array(
      "name" => "test.jpg",
      "type" => "image/jpeg",
      "size" => 19100,
      "tmp_name" => __DIR__ . "/testFile/test.jpg",
      "error" => 0
    );
    
    $this->obj->filesCheck($files, $this->ext_denied, $this->ext_allows, $this->maxmemory, $this->max);
    $this->assertEmpty($this->obj->err);
    $this->assertEquals($this->obj->submitFile["添付ファイル"]["name"], "test.jpg");
  }
  
  public function testOverSizeFile() {
    $files = array();
    $files["添付ファイル"] = array(
      "name" => "test2.jpg",
      "type" => "image/jpeg",
      "size" => 247872,
      "tmp_name" => __DIR__ . "/testFile/test2.jpg",
      "error" => 0
    );
    
    $this->obj->filesCheck($files, $this->ext_denied, $this->ext_allows, $this->maxmemory, $this->max);
    $this->assertEquals($this->obj->err["添付ファイル"], "ファイルの容量が大きすぎます<br>\n");
  }
  
  public function testOtherTypeFile() {
    $files = array();
    $files["添付ファイル"] = array(
      "name" => "test.pdf",
      "type" => "application/pdf",
      "size" => 38308,
      "tmp_name" => __DIR__ . "/testFile/test.pdf",
      "error" => 0
    );
    
    $this->obj->filesCheck($files, $this->ext_denied, $this->ext_allows, $this->maxmemory, $this->max);
    $errMessage = "添付できないファイルです<br>\n";
    $errMessage .= "添付可能なファイルの種類（拡張子）は[".implode("・", $this->ext_allows)."]です\n";
    $this->assertEquals($this->obj->err["添付ファイル"], $errMessage);
  }
}
