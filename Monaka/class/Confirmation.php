<?php

class Confirmation {

  public $adminMail;
  public $adminArray;
  public $requiredItem = array();
  public $submitContent = array();
  public $err = array();
  public $nameCheck = false;
  public $mailCheck = false;
  public $fileData = array();
  public $seriousError;

  public function __construct($adminMail) {
    $this->adminArray = explode(",", $adminMail);
    $this->adminMail = trim($this->adminArray[0]);
  }

  public function postCheck($post) {
    foreach ($post as $key => $values) {

      // $params = explode(",", $values["params"]); /* 今後in_arrayと組み合わせて使うかも?*/

      // 配列(checkbox)を変数に変換
      if (isset($values["value"]) && is_array($values["value"])) {
        $values["value"] = implode("、", $values["value"]);
      }

      // 名前チェック
      if (strpos($values["params"], "名前") !== false) {
        $this->nameCheck = true;
        if (empty($values["value"])) {
          $this->err[$key] = "必須項目です。";
        }
        $this->submitContent[$key] = $values["value"];
        $this->requiredItem["name"] = $values["value"];
        continue;
      }

      // メールチェック
      if (strpos($values["params"], "メール") !== false) {
        $this->mailCheck = true;
        if (empty($values["value"])) {
          $this->err[$key] = "必須項目です。";
        } else {
          // メールアドレスの形式チェック
          if (!$this->mailCheck($values["value"])) {
            $this->err[$key] = "メールアドレスの形式が正しくありません。";
          }
          /* filter_varを使用する場合は下記
          if (!filter_var($this->submitContent["mailaddress"], FILTER_VALIDATE_EMAIL)) {
            $this->err['mailaddress'] = "メールアドレスの形式が正しくありません";
          }
          */
        }
        $this->submitContent[$key] = $values["value"];
        $this->requiredItem["mailaddress"] = $values["value"];
        continue;
      }

      // メール再入力チェック
      if (strpos($values["params"], "再入力") !== false) {
        if ($this->requiredItem["mailaddress"] !== $values["value"]) {
          $this->err[$key] = "メールアドレスが一致しません。";
        } else {
          continue;
        }
      }

      // 電話番号チェック
      if (strpos($values["params"], "電話番号") !== false) {
        if (!empty($values["value"])) {
          if (!$this->telCheck($values["value"])) {
            $this->err[$key] = "電話番号を正しく入力してください。";
          }
        }
      }

      // 郵便番号チェック
      if (strpos($values["params"], "郵便番号") !== false) {
        if (!empty($values["value"])) {
          if (!$this->zipCheck($values["value"])) {
            $this->err[$key] = "郵便番号を正しく入力してください。";
          }
        }
      }

      // 必須チェック
      if (strpos($values["params"], "必須") !== false) {
        if (empty($values["value"])) {
          $this->err[$key] = "必須項目です。";
        }
      }

      $this->submitContent[$key] = isset($values["value"]) ? $values["value"] : '';

    }
  }

  public function mailCheck($email) {
    if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
      return true;
    } else {
      return false;
    }
  }

  public function telCheck($tel) {
    $tel = mb_convert_kana($tel, "n", "UTF-8");
    if (strpos($tel,"-")===false) { //ハイフンなし
      if (preg_match("/(^(?<!090|080|070)\d{10}$)|(^(090|080|070)\d{8}$)|(^0120\d{6}$)|(^0080\d{7}$)/", $tel)) {
        return true;
      } else {
        return false;
      }
    } else { //ハイフンあり
      if (preg_match("/(^(?<!090|080|070)(^\d{2,5}?\-\d{1,4}?\-\d{4}$|^[\d\-]{12}$))|(^(090|080|070)(\-\d{4}\-\d{4}|[\\d-]{13})$)|(^0120(\-\d{2,3}\-\d{3,4}|[\d\-]{12})$)|(^0080\-\d{3}\-\d{4})/", $tel)) {
        return true;
      } else {
        return false;
      }
    }
  }

  public function zipCheck($zip) {
    if (preg_match("/(^\d{3}\-\d{4}$)|(^\d{7}$)/", $zip)) {
      return true;
    } else {
      return false;
    }
  }

  public function setToken() {
    $_SESSION['token'] = sha1(uniqid(mt_rand(), true));
  }

  public function checkToken() {
    if (empty($_POST['token']) || ($_SESSION['token'] != $_POST['token'])) {
      echo "不正な送信です。";
      exit;
    }
  }

  public function filesCheck($files, $ext_denied, $EXT_ALLOWS, $maxmemory, $max) {
    $_SESSION["fileData"] = array();
    if (!empty($files)) {
      foreach ($files as $key => $value) {

        // phpiniの設定によるUPLOAD_ERRのチェック
        if ($value["error"] != UPLOAD_ERR_OK && $value['error'] !== 4) {
          if ($value["error"] === 1) {
            $this->err[$key] = "ファイルの容量が大きすぎます<br>\n";
            $_SESSION["fileData"][$key]["name"] = $value["name"];
          } else {
            $this->err[$key] = "原因不明のエラーです<br>\n";
            $_SESSION["fileData"][$key]["name"] = $value["name"];
          }
          continue;
        }

        if (!empty($value["tmp_name"])) {
          $this->fileData["tmp"] = $value["tmp_name"];
          $this->fileData["name"] = $value["name"];
          $this->fileData["size"] = $value["size"];
          $this->fileData["array"] = explode(".", $this->fileData["name"]);
          $this->fileData["nr"] = count($this->fileData["array"]);
          $this->fileData["ext"] = $this->fileData["array"][$this->fileData["nr"] - 1];

          // config.phpの拡張子制限チェック
          if ($ext_denied == 1 && !@in_array($this->fileData["ext"], $EXT_ALLOWS)) {
            $this->err[$key] = "添付できないファイルです<br>\n";
            $this->err[$key] .= "添付可能なファイルの種類（拡張子）は[".implode("・", $EXT_ALLOWS)."]です\n";
            $_SESSION["fileData"][$key]["name"] = $this->fileData["name"];
            continue;
          }

          // config.phpのアップロード容量制限チェック
          $size = filesize($value['tmp_name']);
          if ($maxmemory == 1 && ($size / 1024) > $max) {
            $this->err[$key] = "ファイルの容量が大きすぎます<br>\n";
            $_SESSION["fileData"][$key]["name"] = $this->fileData["name"];
            continue;
          }

          $fp = fopen($this->fileData["tmp"], "r");
          $contents = fread($fp, filesize($this->fileData["tmp"]));
          fclose($fp);

          $_SESSION["fileData"][$key]["name"] = $this->fileData["name"];
          $_SESSION["fileData"][$key]["tmp"] = $this->fileData["tmp"];
          $_SESSION["fileData"][$key]["ext"] = $this->fileData["ext"];
          $_SESSION["fileData"][$key]["file"] = chunk_split(base64_encode($contents)); //エンコードして分割
        }
      }
    }
  }

  public function seriousErrorCheck($contentLength) {
    if (strpos(ini_get("post_max_size"), "M") !== false) {
      $postMaxSize = ini_get("post_max_size") * 1024 * 1024;
    } else {
      $postMaxSize = ini_get("post_max_size") * 1;
    }
    if ($contentLength > $postMaxSize) {
      $this->seriousError = "ファイルサイズの総量が大きすぎる可能性があります。<br>\n";
      $this->seriousError .= "再度お試しいただき、解消しない場合は、<br>\n";
      $this->seriousError .= "管理者【{$this->adminMail}】にお知らせください。";
    } elseif (!$this->nameCheck || !$this->mailCheck) {
      $this->seriousError = "エラーが発生しました。<br>\n";
      $this->seriousError .= "再度お試しいただき、解消しない場合は、<br>\n";
      $this->seriousError .= "管理者【{$this->adminMail}】にお知らせください。";
    }
  }

}
