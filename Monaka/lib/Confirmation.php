<?php

namespace Monaka;

class Confirmation {

  public $adminMail;
  public $adminArray;
  public $requiredItem = array();
  public $err = array();
  private $_nameCheck = false;
  private $_mailCheck = false;
  public $fileData = array();
  public $seriousError;
  private $checkExt = array();
  private $_extMimeMap = array(
    "jpg" => array("image/jpeg"),
    "jpeg" => array("image/jpeg"),
    "gif" => array("image/gif"),
    "png" => array("image/png"),
    "pdf" => array("application/pdf"),
    "txt" => array("text/plain"),
    "csv" => array("text/plain", "text/csv", "application/csv"),
    "zip" => array("application/zip", "application/x-zip-compressed"),
    "doc" => array("application/msword"),
    "docx" => array("application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/zip"),
    "xls" => array("application/vnd.ms-excel"),
    "xlsx" => array("application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/zip"),
  );

  public function run($adminMail, $ext_denied, $EXT_ALLOWS, $maxmemory, $max, $contentLength) {
    $this->adminArray = explode(",", $adminMail);
    $this->adminMail = trim($this->adminArray[0]);
    $this->_setToken();
    $this->_postCheck($_POST);
    $this->_filesCheck($ext_denied, $EXT_ALLOWS, $maxmemory, $max);
    $this->_seriousErrorCheck($contentLength);
  }

  private function _setToken() {
    $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16));
  }

  private function _postCheck($post) {
    if (isset($_SESSION["submitContent"])) {
      unset($_SESSION["submitContent"]);
    }

    foreach ($post as $key => $values) {
      // $params = explode(",", $values["params"]); /* 今後in_arrayと組み合わせて使うかも?*/
      // 配列(checkbox)を変数に変換
      if (isset($values["value"]) && is_array($values["value"])) {
        $values["value"] = implode("、", $values["value"]);
      }

      // 添付ファイル必須化の為にparamsだけPOSTされた場合はcontinue
      if (!empty($_FILES)) {
        $flg = false;
        foreach ($_FILES as $k => $v) {
          if ($key == $k) {
            $flg = true;
          }
        }
        if ($flg) continue;
      }

      // 名前チェック
      if (isset($values["params"]) && strpos($values["params"], "名前") !== false) {
        $this->_nameCheck = true;
        if (empty($values["value"])) {
          $this->err[$key] = "必須項目です。";
        } elseif (preg_match("/[\r\n]/", $values["value"])) {
          // メールヘッダーインジェクション対策(送信者名はメールのFromヘッダーに使われるため改行を禁止する)
          $this->err[$key] = "改行を含む入力は許可されていません。";
        }
        $_SESSION["submitContent"][$key] = $values["value"];
        $this->requiredItem["name"] = $values["value"];
        continue;
      }

      // メールチェック
      if (isset($values["params"]) && strpos($values["params"], "メール") !== false) {
        $this->_mailCheck = true;
        if (empty($values["value"])) {
          $this->err[$key] = "必須項目です。";
        } else {
          // メールアドレスの形式チェック
          if (!$this->_mailCheck($values["value"])) {
            $this->err[$key] = "メールアドレスの形式が正しくありません。";
          }
          /* filter_varを使用する場合は下記
          if (!filter_var($_SESSION["submitContent"]["mailaddress"], FILTER_VALIDATE_EMAIL)) {
            $this->err['mailaddress'] = "メールアドレスの形式が正しくありません";
          }
          */
        }
        $_SESSION["submitContent"][$key] = $values["value"];
        $this->requiredItem["mailaddress"] = $values["value"];
        continue;
      }

      // メール再入力チェック
      if (isset($values["params"]) && strpos($values["params"], "再入力") !== false) {
        if ($this->requiredItem["mailaddress"] !== $values["value"]) {
          $this->err[$key] = "メールアドレスが一致しません。";
        } else {
          continue;
        }
      }

      // 電話番号チェック
      if (isset($values["params"]) && strpos($values["params"], "電話番号") !== false) {
        if (!empty($values["value"])) {
          if (!$this->_telCheck($values["value"])) {
            $this->err[$key] = "電話番号を正しく入力してください。";
          }
        }
      }

      // 郵便番号チェック
      if (isset($values["params"]) && strpos($values["params"], "郵便番号") !== false) {
        if (!empty($values["value"])) {
          if (!$this->_zipCheck($values["value"])) {
            $this->err[$key] = "郵便番号を正しく入力してください。";
          }
        }
      }

      // 必須チェック
      if (isset($values["params"]) && strpos($values["params"], "必須") !== false) {
        if (!isset($values["value"]) || (empty($values["value"]) && (string)$values["value"] !== "0")) {
          $this->err[$key] = "必須項目です。";
        }
      }

      if (isset($values["value"])) {
        $value = explode("\n", $values["value"]);
        foreach ($value as $val) {
          if (strlen(mb_convert_encoding($val, "SJIS", "UTF-8")) > 980) {
            $this->err[$key] = "長文を改行なしで入力されているようです。<br>" . PHP_EOL;
            $this->err[$key] .= "このまま送信すると文字化けしてしまうため、" . PHP_EOL;
            $this->err[$key] .= "490文字以内で改行してください。" . PHP_EOL;
            break;
          }
        }
        $_SESSION["submitContent"][$key] = $values["value"];
      } else {
        $_SESSION["submitContent"][$key] = '';
      }
    }
  }

  private function _mailCheck($email) {
    if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
      return true;
    } else {
      return false;
    }
  }

  private function _telCheck($tel) {
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

  private function _zipCheck($zip) {
    if (preg_match("/(^\d{3}\-\d{4}$)|(^\d{7}$)/", $zip)) {
      return true;
    } else {
      return false;
    }
  }

  private function _filesCheck($ext_denied, $EXT_ALLOWS, $maxmemory, $max) {
    $_SESSION["fileData"] = array();
    if (!empty($_FILES)) {
      foreach ($_FILES as $key => $value) {

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

          // ファイル名に改行が含まれる場合は拒否(メール添付名としてのヘッダー分割対策)
          if (preg_match("/[\r\n]/", $this->fileData["name"])) {
            $this->err[$key] = "添付できないファイルです<br>\n";
            $this->err[$key] .= "ファイル名に改行を含めることはできません\n";
            $_SESSION["fileData"][$key]["name"] = $this->fileData["name"];
            continue;
          }

          // config.phpの拡張子制限チェック
          $this->checkExt = array();
          foreach ($EXT_ALLOWS as $ext) {
            $this->checkExt[] = strtolower($ext);
            $this->checkExt[] = strtoupper($ext);
          }
          if ($ext_denied == 1 && !in_array($this->fileData["ext"], $this->checkExt)) {
            $this->err[$key] = "添付できないファイルです<br>\n";
            $this->err[$key] .= "添付可能なファイルの種類（拡張子）は[".implode("・", $EXT_ALLOWS)."]です\n";
            $_SESSION["fileData"][$key]["name"] = $this->fileData["name"];
            continue;
          }

          // 拡張子偽装対策(ファイルの実際の中身と拡張子が一致するかチェック)
          $extLower = strtolower($this->fileData["ext"]);
          if ($ext_denied == 1 && isset($this->_extMimeMap[$extLower])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $realMime = finfo_file($finfo, $value["tmp_name"]);
            finfo_close($finfo);
            if (!in_array($realMime, $this->_extMimeMap[$extLower], true)) {
              $this->err[$key] = "添付できないファイルです<br>\n";
              $this->err[$key] .= "ファイルの内容が拡張子と一致しません\n";
              $_SESSION["fileData"][$key]["name"] = $this->fileData["name"];
              continue;
            }
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
          $_SESSION["fileData"][$key]["ext"] = $this->fileData["ext"];
          $_SESSION["fileData"][$key]["file"] = $contents;
        } else {
          if (isset($_POST[$key]["params"]) && strpos($_POST[$key]["params"], "必須") !== false) {
            $this->err[$key] = "必須項目です。<br>\n";
            $_SESSION["fileData"][$key]["name"] = "";
            continue;
          }
        }
      }
    }
  }

  private function _iniSizeToBytes($value) {
    $value = trim((string) $value);
    $unit = strtoupper(substr($value, -1));
    $number = (int) $value;
    switch ($unit) {
      case 'G':
        return $number * 1024 * 1024 * 1024;
      case 'M':
        return $number * 1024 * 1024;
      case 'K':
        return $number * 1024;
      default:
        return $number;
    }
  }

  private function _seriousErrorCheck($contentLength) {
    $postMaxSize = $this->_iniSizeToBytes(ini_get("post_max_size"));
    if ($contentLength > $postMaxSize) {
      $this->seriousError = "ファイルサイズの総量が大きすぎる可能性があります。<br>\n";
      $this->seriousError .= "再度お試しいただき、解消しない場合は、<br>\n";
      $this->seriousError .= "管理者【{$this->adminMail}】にお知らせください。";
    } elseif (!$this->_nameCheck || !$this->_mailCheck) {
      $this->seriousError = "エラーが発生しました。<br>\n";
      $this->seriousError .= "再度お試しいただき、解消しない場合は、<br>\n";
      $this->seriousError .= "管理者【{$this->adminMail}】にお知らせください。";
    }
  }

}
