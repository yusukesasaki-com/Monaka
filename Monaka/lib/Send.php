<?php

namespace Monaka;

class Send {

  private $_mailer;
  private $_adminMail;
  private $_adminArray = array();
  private $_adminName;
  private $_requiredItem = array();
  private $_submitContent = array();
  public $sendMessage;
  public $returnMessage;
  public $adminMailError;
  public $returnMailError;

  public function __construct(?Mailer $mailer = null) {
    $this->_mailer = $mailer ?? new Mailer();
  }

  public function run($adminMail, $adminName, $returnMailTitle, $returnMailHeader, $returnMailFooter) {
    $this->_adminArray = explode(",", $adminMail);
    $this->_adminMail = trim($this->_adminArray[0]);
    $this->_adminName = $adminName;
    $this->_checkToken();
    $this->_substitutionSubmitContent($_SESSION["submitContent"] ?? array());
    $this->_substitutionRequiredItem($_POST["requiredItem"] ?? array());
    $server = array(
      "REMOTE_ADDR" => $_SERVER["REMOTE_ADDR"],
      "REMOTE_HOST" => gethostbyaddr($_SERVER['REMOTE_ADDR']),
      "HTTP_USER_AGENT" => $_SERVER["HTTP_USER_AGENT"]
    );
    $this->_adminSend($_SESSION["submitFile"] ?? array(), $server);
    $this->_returnSend($returnMailTitle, $returnMailHeader, $returnMailFooter);
    $this->_sessionReset();
  }

  private function _checkToken() {
    if (
      !isset($_POST['token']) ||
      !isset($_SESSION['token']) ||
      $_SESSION['token'] !== $_POST['token']
    ) {
      echo "不正な送信です。";
      exit;
    }
  }

  private function _substitutionSubmitContent($post) {
    foreach ($post as $key => $value) {
      $this->_submitContent[$key] = $value;
    }
  }

  private function _substitutionRequiredItem($post) {
    foreach ($post as $key => $value) {
      $this->_requiredItem[$key] = $value;
    }
  }

  private function _adminSend($submitFile, $server) {
    $name = $this->_requiredItem["name"] ?? '';
    $mailaddress = $this->_requiredItem["mailaddress"] ?? '';

    $sendTitle = "{$name}様よりメールが届きました。";

    $this->sendMessage = "{$name}様より、下記内容でメールが届きました。\n";
    $this->sendMessage .= "\n";
    foreach ($this->_submitContent as $key => $value) {
      $this->sendMessage .= "■{$key}\n";
      $this->sendMessage .= "{$value}\n\n";
    }
    $this->sendMessage .= "\n\n";
    $this->sendMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $this->sendMessage .= "[送信日時]".date("Y年m月d日(D) H時i分s秒")."\n";
    $this->sendMessage .= "[IPアドレス]{$server["REMOTE_ADDR"]}\n";
    $this->sendMessage .= "[ホスト]{$server["REMOTE_HOST"]}\n";
    $this->sendMessage .= "[USER_AGENT]{$server["HTTP_USER_AGENT"]}\n";
    $this->sendMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    $attachments = array();
    foreach ($submitFile as $files) {
      foreach ($files as $fileName => $encoded) {
        $attachments[] = array(
          "content" => base64_decode($encoded),
          "filename" => $fileName,
        );
      }
    }

    $to = array();
    foreach ($this->_adminArray as $value) {
      $to[] = array(trim($value), $this->_adminName);
    }

    // From:には自ドメインの送信者アドレスを使い、visitorのアドレスはReply-Toに設定する。
    // (visitorのメールアドレスをFromに使うとSPF/DKIM不一致でSMTP送信時に拒否されることがあるため)
    $result = $this->_mailer->send(
      $to,
      $this->_adminMail,
      $this->_adminName,
      $sendTitle,
      $this->sendMessage,
      $attachments,
      $mailaddress,
      $name
    );

    if ($result !== true) {
      $this->adminMailError = $result;
    }
  }

  private function _returnSend($returnMailTitle, $returnMailHeader, $returnMailFooter) {
    $name = $this->_requiredItem["name"] ?? '';
    $mailaddress = $this->_requiredItem["mailaddress"] ?? '';

    $returnTitle = "【{$this->_adminName}】 {$returnMailTitle}";

    $this->returnMessage = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $this->returnMessage .= "【{$this->_adminName}】 {$returnMailTitle}\n";
    $this->returnMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $this->returnMessage .= "\n";
    $this->returnMessage .= "{$name}様\n";
    $this->returnMessage .= "\n";
    $this->returnMessage .= $returnMailHeader;
    $this->returnMessage .= "\n";
    $this->returnMessage .= "----------------------------------------------------------------------\n";
    foreach ($this->_submitContent as $key => $value) {
      $this->returnMessage .= "■{$key}\n";
      $this->returnMessage .= "{$value}\n\n";
    }
    $this->returnMessage .= "----------------------------------------------------------------------\n";
    $this->returnMessage .= "\n";
    $this->returnMessage .= $returnMailFooter;
    $this->returnMessage .= "\n";

    $result = $this->_mailer->send(
      array(array($mailaddress, $name)),
      $this->_adminMail,
      $this->_adminName,
      $returnTitle,
      $this->returnMessage
    );

    if ($result !== true) {
      $this->returnMailError = $result;
    }
  }

  private function _sessionReset() {
    $_SESSION["token"] = array();
    $_SESSION["fileData"] = array();
    $_SESSION["submitFile"] = array();
    $_SESSION["submitContent"] = array();
  }

}
