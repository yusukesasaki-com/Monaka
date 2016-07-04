<?php

class Send {

  public $adminMail;
  public $adminArray = array();
  public $adminName;
  public $returnMailHeader;
  public $returnMailFooter;
  public $requiredItem = array();
  public $submitContent = array();
  public $submitFile = array();
  public $boundary;
  public $sendMail = array();
  public $sendTitle;
  public $sendMessage;
  public $sendHeaders;
  public $returnMail;
  public $returnTitle;
  public $returnMessage;
  public $returnHeaders;
  public $server;

  public function __construct($adminMail, $adminName, $returnMailHeader, $returnMailFooter, $submitFile, $server) {
    $this->adminArray = explode(",", $adminMail);
    $this->adminMail = trim($this->adminArray[0]);
    $this->adminName = $adminName;
    $this->returnMailHeader = $returnMailHeader;
    $this->returnMailFooter = $returnMailFooter;
    $this->submitFile = $submitFile;
    $this->server = array(
      "REMOTE_ADDR" => $server["REMOTE_ADDR"],
      "REMOTE_HOST" => gethostbyaddr($server['REMOTE_ADDR']),
      "HTTP_USER_AGENT" => $server["HTTP_USER_AGENT"]
    );
  }

  public function substitutionRequiredItem($post) {
    foreach ($post as $key => $value) {
      $this->requiredItem[$key] = $this->replaceText($value);
    }
  }

  public function substitutionSubmitContent($post) {
    foreach ($post as $key => $value) {
      $this->submitContent[$key] = $this->replaceText($value);
    }
  }

  public function characterSetting() {
    // メールの言語・文字コードの設定
    mb_language("Japanese");
    mb_internal_encoding("UTF-8");

    // バウンダリー文字（パートの境界）
    $this->boundary = md5(uniqid(rand()));
  }

  public function adminSend() {
    // 送信先の設定
    foreach ($this->adminArray as $value) {
      $this->sendMail[] = mb_encode_mimeheader($this->adminName, "ISO-2022-JP-MS","UTF-8") ." <" . trim($value) . ">";
    }

    // タイトルの設定
    $this->sendTitle = "{$this->requiredItem["name"]}様よりお問い合わせ";
    $this->sendTitle = mb_encode_mimeheader($this->sendTitle, "ISO-2022-JP-MS","UTF-8");

    // メッセージの設定
    $this->sendMessage = "{$this->requiredItem["name"]}様より、下記内容でお問い合わせが届いています。\n";
    $this->sendMessage .= "\n";
    foreach ($this->submitContent as $key => $value) {
      $this->sendMessage .= "■{$key}\n";
      $this->sendMessage .= "{$value}\n\n";
    }
    $this->sendMessage .= "\n\n";
    $this->sendMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $this->sendMessage .= "[送信日時]".date("Y年m月d日(D) H時i分s秒")."\n";
    $this->sendMessage .= "[IPアドレス]{$this->server["REMOTE_ADDR"]}\n";
    $this->sendMessage .= "[ホスト]{$this->server["REMOTE_HOST"]}\n";
    $this->sendMessage .= "[USER_AGENT]{$this->server["HTTP_USER_AGENT"]}\n";
    $this->sendMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $this->sendMessage = mb_convert_encoding($this->sendMessage, "ISO-2022-JP-MS","UTF-8");

    //ヘッダーの設定
    $this->sendHeaders = "X-Mailer: PHP5\r\n";
    $this->sendHeaders = "MIME-Version: 1.0\r\n";
    $this->sendHeaders .= "From: ".mb_encode_mimeheader($this->requiredItem["name"], "ISO-2022-JP-MS","UTF-8") ." <{$this->requiredItem["mailaddress"]}> \r\n";
    $this->sendHeaders .= "Content-Transfer-Encoding: 7bit\r\n";

    // 添付ファイルの設定
    if (!empty($this->submitFile)) {
      $this->sendHeaders .= "Content-type: multipart/mixed; boundary=\"{$this->boundary}\" \r\n";

      $tmpMessage = $this->sendMessage;

      $this->sendMessage = "--{$this->boundary}\n";
      $this->sendMessage .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
      $this->sendMessage .= "Content-Transfer-Encoding: 7bit\n\n";
      $this->sendMessage .= $tmpMessage."\n";

      foreach ($this->submitFile as $key => $value) {
        foreach ($value as $key2 => $value2) {
          $name = $key2;
          $f_encoded = $value2;

          $this->sendMessage .= "\n";
          $this->sendMessage .= "--{$this->boundary}\n";
          $this->sendMessage .= "Content-Type: application/octet-stream; ";
          $this->sendMessage .= "charset=\"ISO-2022-JP\" ";
          $this->sendMessage .= "name=\"".mb_encode_mimeheader($name, "ISO-2022-JP-MS","UTF-8")."\"\n";
          $this->sendMessage .= "Content-Transfer-Encoding: base64\n";
          $this->sendMessage .= "Content-Disposition: attachment; ";
          $this->sendMessage .= "filename=\"".mb_encode_mimeheader($name, "ISO-2022-JP-MS","UTF-8")."\"\n";
          $this->sendMessage .= "\n";
          $this->sendMessage .= "{$f_encoded}\n";
        }
      }

      $this->sendMessage .= "--{$this->boundary}--\n";

    } else {
      $this->sendHeaders .= "Content-type: text/plain; charset=\"ISO-2022-JP\" \r\n";
    }

    // メールの送信 (宛先, 件名, 本文, 送り主(From:が必須))
    foreach ($this->sendMail as $send) {
      @mail($send, $this->sendTitle, $this->sendMessage, $this->sendHeaders);
    }
  }

  public function returnSend() {
    // 送信先の設定
    $this->returnMail = mb_encode_mimeheader($this->requiredItem["name"], "ISO-2022-JP-MS","UTF-8") ." <{$this->requiredItem["mailaddress"]}>";

    // タイトルの設定
    $this->returnTitle = "【{$this->adminName}】 お問い合わせを受け付けました";
    $this->returnTitle = mb_encode_mimeheader($this->returnTitle, "ISO-2022-JP-MS","UTF-8");

    // メッセージの設定
    $this->returnMessage = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $this->returnMessage .= "【{$this->adminName}】 お問い合わせを受け付けました\n";
    $this->returnMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $this->returnMessage .= "\n";
    $this->returnMessage .= "\n";
    $this->returnMessage .= $this->returnMailHeader;
    $this->returnMessage .= "\n";
    $this->returnMessage .= "----------------------------------------------------------------------\n";
    foreach ($this->submitContent as $key => $value) {
      $this->returnMessage .= "■{$key}\n";
      $this->returnMessage .= "{$value}\n\n";
    }
    $this->returnMessage .= "----------------------------------------------------------------------\n";
    $this->returnMessage .= "\n";
    $this->returnMessage .= $this->returnMailFooter;
    $this->returnMessage .= "\n";
    $this->returnMessage = mb_convert_encoding($this->returnMessage, "ISO-2022-JP-MS","UTF-8");

    //ヘッダーの設定
    $this->returnHeaders = "MIME-Version: 1.0\r\n";
    $this->returnHeaders .= "Content-type: text/plain; charset=ISO-2022-JP\r\n";
    $this->returnHeaders .= "From: ".mb_encode_mimeheader($this->adminName, "ISO-2022-JP-MS","UTF-8") ." <{$this->adminMail}> \r\n";

    // メールの送信 (宛先, 件名, 本文, 送り主(From:が必須))
    @mail($this->returnMail, $this->returnTitle, $this->returnMessage, $this->returnHeaders);
  }

  public function checkToken() {
    if (empty($_POST['token']) || ($_SESSION['token'] != $_POST['token'])) {
      echo "不正な送信です。";
      exit;
    }
  }

  public function sessionReset() {
    $_SESSION["token"] = array();
    $_SESSION["fileData"] = array();
    $_SESSION["submitFile"] = array();
  }

  public function replaceText($str) {
    $arr = array(
      "\xE2\x84\xA2" => 'TM',
      "\xE2\x85\x93" => '1/3',
      "\xE2\x85\x94" => '2/3',
      "\xE2\x85\x95" => '1/5',
      "\xE2\x85\x96" => '2/5',
      "\xE2\x85\x97" => '3/5',
      "\xE2\x85\x98" => '4/5',
      "\xE2\x85\x99" => '1/6',
      "\xE2\x85\x9A" => '5/6',
      "\xE2\x85\x9B" => '1/8',
      "\xE2\x85\x9C" => '3/8',
      "\xE2\x85\x9D" => '5/8',
      "\xE2\x85\x9E" => '7/8',
      "\xE2\x85\x9F" => '1/ ',
      "\xE2\x86\x90" => '<-',
      "\xE2\x86\x91" => '(上矢印)',
      "\xE2\x86\x92" => '->',
      "\xE2\x86\x93" => '(下矢印)',
      "\xE2\x86\x94" => '<->',
      "\xE2\x86\x95" => '(上下矢印)',
      "\xE2\x86\x96" => '(左上矢印)',
      "\xE2\x86\x97" => '(右上矢印)',
      "\xE2\x86\x98" => '(右下矢印)',
      "\xE2\x86\x99" => '(左下矢印)',
      "\xE2\x86\x9A" => '<-/-',
      "\xE2\x86\x9B" => '-/->',
      "\xE2\x86\x9C" => '<~',
      "\xE2\x86\x9D" => '~>',
      "\xE2\x86\x9E" => '<<--',
      "\xE2\x86\x9F" => '-->>',
      "\xE2\x98\x80" => '(晴)',
      "\xE2\x98\x81" => '(曇)',
      "\xE2\x98\x82" => '(雨)',
      "\xE2\x98\x83" => '(雪)',
      "\xE2\x98\x8E" => '(黒電話)',
      "\xE2\x98\x8F" => '(白電話)',
      "\xE2\x98\x90" => '(チェックボックス 空欄)',
      "\xE2\x98\x91" => '(チェックボックス チェック)',
      "\xE2\x98\x92" => '(チェックボックス チェック)',
      "\xE2\x98\x93" => '(チェック)',
      "\xE3\x8C\x80" => 'アパート',
      "\xE3\x8C\x81" => 'アルファ',
      "\xE3\x8C\x82" => 'アンペア',
      "\xE3\x8C\x83" => 'アール',
      "\xE3\x8C\x84" => 'イニング',
      "\xE3\x8C\x85" => 'インチ',
      "\xE3\x8C\x86" => 'ウォン',
      "\xE3\x8C\x87" => 'エスクード',
      "\xE3\x8C\x88" => 'エーカー',
      "\xE3\x8C\x89" => 'オンス',
      "\xE3\x8C\x8A" => 'オーム',
      "\xE3\x8C\x8B" => 'カイリ',
      "\xE3\x8C\x8C" => 'カラット',
      "\xE3\x8C\x8D" => 'カロリー',
      "\xE3\x8C\x8E" => 'ガロン',
      "\xE3\x8C\x8F" => 'ガンマ',
      "\xE3\x8C\x90" => 'ギガ',
      "\xE3\x8C\x91" => 'ギニー',
      "\xE3\x8C\x92" => 'キュリー',
      "\xE3\x8C\x93" => 'ギルダー',
      "\xE3\x8C\x94" => 'キロ',
      "\xE3\x8C\x95" => 'キログラム',
      "\xE3\x8C\x96" => 'キロメートル',
      "\xE3\x8C\x97" => 'キロワット',
      "\xE3\x8C\x98" => 'グラム',
      "\xE3\x8C\x99" => 'グラムトン',
      "\xE3\x8C\x9A" => 'クルゼイロ',
      "\xE3\x8C\x9B" => 'クローネ',
      "\xE3\x8C\x9C" => 'ケース',
      "\xE3\x8C\x9D" => 'コルナ',
      "\xE3\x8C\x9E" => 'コーポ',
      "\xE3\x8C\x9F" => 'サイクル',
      "\xE3\x8C\xA0" => 'サンチーム',
      "\xE3\x8C\xA1" => 'シリング',
      "\xE3\x8C\xA2" => 'センチ',
      "\xE3\x8C\xA3" => 'セント',
      "\xE3\x8C\xA4" => 'ダース',
      "\xE3\x8C\xA5" => 'デシ',
      "\xE3\x8C\xA6" => 'ドル',
      "\xE3\x8C\xA8" => 'ナノ',
      "\xE3\x8C\xA9" => 'ノット',
      "\xE3\x8C\xAA" => 'ハイツ',
      "\xE3\x8C\xAB" => 'パーセント',
      "\xE3\x8C\xAC" => 'パーツ',
      "\xE3\x8C\xAD" => 'バーレル',
      "\xE3\x8C\xAE" => 'ピアストル',
      "\xE3\x8C\xAF" => 'ピクル',
      "\xE3\x8C\xB0" => 'ピコ',
      "\xE3\x8C\xB1" => 'ビル',
      "\xE3\x8C\xB2" => 'ファラッド',
      "\xE3\x8C\xB3" => 'フィート',
      "\xE3\x8C\xB4" => 'ブッシェル',
      "\xE3\x8C\xB5" => 'フラン',
      "\xE3\x8C\xB6" => 'ヘクタール',
      "\xE3\x8C\xB7" => 'ペソ',
      "\xE3\x8C\xB8" => 'ペニヒ',
      "\xE3\x8C\xB9" => 'ヘルツ',
      "\xE3\x8C\xBA" => 'ペンス',
      "\xE3\x8C\xBB" => 'ページ',
      "\xE3\x8C\xBC" => 'ベータ',
      "\xE3\x8C\xBD" => 'ポイント',
      "\xE3\x8C\xBE" => 'ボルト',
      "\xE3\x8C\xBF" => 'ホン',
      "\xE3\x8D\x80" => 'ポンド',
      "\xE3\x8D\x81" => 'ホール',
      "\xE3\x8D\x82" => 'ホーン',
      "\xE3\x8D\x83" => 'マイクロ',
      "\xE3\x8D\x84" => 'マイル',
      "\xE3\x8D\x85" => 'マッハ',
      "\xE3\x8D\x86" => 'マルク',
      "\xE3\x8D\x87" => 'マンション',
      "\xE3\x8D\x88" => 'ミクロン',
      "\xE3\x8D\x89" => 'ミリ',
      "\xE3\x8D\x8A" => 'ミリバール',
      "\xE3\x8D\x8B" => 'メガ',
      "\xE3\x8D\x8C" => 'メガトン',
      "\xE3\x8D\x8D" => 'メートル',
      "\xE3\x8D\x8E" => 'ヤード',
      "\xE3\x8D\x8F" => 'ヤール',
      "\xE3\x8D\x90" => 'ユアン',
      "\xE3\x8D\x91" => 'リットル',
      "\xE3\x8D\x92" => 'リラ',
      "\xE3\x8D\x93" => 'ルピー',
      "\xE3\x8D\x94" => 'ルーブル',
      "\xE3\x8D\x95" => 'レム',
      "\xE3\x8D\x96" => 'レントゲン',
      "\xE3\x8D\x97" => 'ワット',
      "\xE3\x8D\xBB" => '平成',
      "\xE3\x8D\xBC" => '昭和',
      "\xE3\x8D\xBD" => '大正',
      "\xE3\x8D\xBE" => '明治',
      "\xE3\x8D\xBF" => '株式会社',
      "\xE2\x92\xB6" => '(A)',
      "\xE2\x92\xB7" => '(B)',
      "\xE2\x92\xB8" => '(C)',
      "\xE2\x92\xB9" => '(D)',
      "\xE2\x92\xBA" => '(E)',
      "\xE2\x92\xBB" => '(F)',
      "\xE2\x92\xBC" => '(G)',
      "\xE2\x92\xBD" => '(H)',
      "\xE2\x92\xBE" => '(I)',
      "\xE2\x92\xBF" => '(J)',
      "\xE2\x93\x80" => '(K)',
      "\xE2\x93\x81" => '(L)',
      "\xE2\x93\x82" => '(M)',
      "\xE2\x93\x83" => '(N)',
      "\xE2\x93\x84" => '(O)',
      "\xE2\x93\x85" => '(P)',
      "\xE2\x93\x86" => '(Q)',
      "\xE2\x93\x87" => '(R)',
      "\xE2\x93\x88" => '(S)',
      "\xE2\x93\x89" => '(T)',
      "\xE2\x93\x8A" => '(U)',
      "\xE2\x93\x8B" => '(V)',
      "\xE2\x93\x8C" => '(W)',
      "\xE2\x93\x8D" => '(X)',
      "\xE2\x93\x8E" => '(Y)',
      "\xE2\x93\x8F" => '(Z)',
      "\xE2\x99\xA0" => '(スペード)',
      "\xE2\x99\xA1" => '(ハード)',
      "\xE2\x99\xA2" => '(ダイヤ)',
      "\xE2\x99\xA3" => '(クラブ)',
      "\xE2\x99\xA4" => '(スペード)',
      "\xE2\x99\xA5" => '(ハード)',
      "\xE2\x99\xA6" => '(ダイヤ)',
      "\xE2\x99\xA7" => '(クラブ)',
      "\xE2\x99\xA8" => '(温泉)',
      "\xE2\x99\xA9" => '(4分音符)',
      "\xE2\x99\xAA" => '(8分音符)',
      "\xE2\x99\xAB" => '(2つの8分音符)',
      "\xE2\x99\xAC" => '(2つの16分音符)',
      "\xE2\x99\xAD" => '(フラット)',
      "\xE2\x99\xAE" => '(ナチュラル)',
      "\xE2\x99\xAF" => '(シャープ)',
      "\xE3\x88\xB3" => '(社)',
      "\xE3\x88\xB4" => '(名)',
      "\xE3\x88\xB5" => '(特)',
      "\xE3\x88\xB6" => '(財)',
      "\xE3\x88\xB7" => '(祝)',
      "\xE3\x88\xB8" => '(労)',
      "\xE3\x88\xB9" => '(代)',
      "\xE3\x88\xBA" => '(呼)',
      "\xE3\x88\xBB" => '(学)',
      "\xE3\x88\xBC" => '(監)',
      "\xE3\x88\xBD" => '(企)',
      "\xE3\x88\xBE" => '(資)',
      "\xE3\x88\xBF" => '(協)',
      "\xE3\x89\x80" => '(祭)',
      "\xE3\x89\x81" => '(休)',
      "\xE3\x89\x82" => '(自)',
      "\xE3\x89\x83" => '(至)',
    );
    return str_replace(array_keys($arr), array_values($arr), $str);
  }

}
