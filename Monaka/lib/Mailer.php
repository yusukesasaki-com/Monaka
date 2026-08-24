<?php

namespace Monaka;

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer {

  private $smtpEnabled;
  private $smtpHost;
  private $smtpPort;
  private $smtpUser;
  private $smtpPass;
  private $smtpSecure;

  public function __construct(
    $smtpEnabled = false,
    $smtpHost = '',
    $smtpPort = 587,
    $smtpUser = '',
    $smtpPass = '',
    $smtpSecure = 'tls'
  ) {
    $this->smtpEnabled = $smtpEnabled;
    $this->smtpHost = $smtpHost;
    $this->smtpPort = $smtpPort;
    $this->smtpUser = $smtpUser;
    $this->smtpPass = $smtpPass;
    $this->smtpSecure = $smtpSecure;
  }

  /**
   * @param array $to [[address, name], ...]
   * @param array $attachments [["content" => 生バイナリ, "filename" => ファイル名], ...]
   * @return true|string 成功時true、失敗時はエラーメッセージ文字列
   */
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
    $mail = new PHPMailer(true);
    try {
      if ($this->smtpEnabled) {
        $mail->isSMTP();
        $mail->Host = $this->smtpHost;
        $mail->Port = $this->smtpPort;
        if ($this->smtpUser !== '') {
          $mail->SMTPAuth = true;
          $mail->Username = $this->smtpUser;
          $mail->Password = $this->smtpPass;
        }
        if ($this->smtpSecure !== '') {
          $mail->SMTPSecure = $this->smtpSecure;
        }
      } else {
        $mail->isMail();
      }

      $mail->CharSet = 'UTF-8';
      $mail->Encoding = 'base64';
      $mail->setFrom($fromAddress, $fromName);

      if ($replyToAddress) {
        $mail->addReplyTo($replyToAddress, $replyToName);
      }

      foreach ($to as $recipient) {
        $mail->addAddress($recipient[0], $recipient[1] ?? '');
      }

      $mail->Subject = $subject;
      $mail->Body = $body;
      $mail->isHTML(false);

      foreach ($attachments as $attachment) {
        $mail->addStringAttachment($attachment['content'], $attachment['filename']);
      }

      $mail->send();
      return true;
    } catch (PHPMailerException $e) {
      return $mail->ErrorInfo !== '' ? $mail->ErrorInfo : $e->getMessage();
    }
  }

}
