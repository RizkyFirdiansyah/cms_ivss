<?php
// app/libs/SimpleMailer.php

class SimpleMailer
{

  // ⚠️ GANTI INI DENGAN KONFIGURASI ANDA!
  private const GMAIL_USER = 'mazrizky46@gmail.com';      // Ganti dengan email Gmail Anda
  private const GMAIL_PASS = 'lvrzaicdsjnztpsx'; // Ganti dengan App Password
  private const FROM_NAME = 'IVSS Laboratory';

  /**
   * Kirim email approval ke mahasiswa
   */
  public static function sendApproval($toEmail, $toName, $password)
  {
    $subject = '🎉 Pendaftaran Disetujui - IVSS Laboratory';
    $loginUrl = self::getBaseUrl() . '/login';

    $html = self::getApprovalHtml($toName, $password, $loginUrl);
    $text = self::getApprovalText($toName, $password, $loginUrl);

    return self::send($toEmail, $toName, $subject, $html, $text);
  }

  /**
   * Kirim email rejection ke mahasiswa
   */
  public static function sendRejection($toEmail, $toName)
  {
    $subject = '📨 Status Pendaftaran - IVSS Laboratory';

    $html = self::getRejectionHtml($toName);
    $text = self::getRejectionText($toName);

    return self::send($toEmail, $toName, $subject, $html, $text);
  }

  /**
   * Fungsi utama untuk kirim email via Gmail SMTP
   */
  private static function send($toEmail, $toName, $subject, $htmlBody, $textBody = '')
  {
    try {
      // Include PHPMailer
      require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
      require_once __DIR__ . '/PHPMailer/src/SMTP.php';
      require_once __DIR__ . '/PHPMailer/src/Exception.php';

      $mail = new PHPMailer\PHPMailer\PHPMailer(true);

      // Server settings
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = self::GMAIL_USER;
      $mail->Password = self::GMAIL_PASS;
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;

      // Disable debug untuk production
      $mail->SMTPDebug = 0;

      // Recipients
      $mail->setFrom(self::GMAIL_USER, self::FROM_NAME);
      $mail->addAddress($toEmail, $toName);
      $mail->addReplyTo(self::GMAIL_USER, 'Admin IVSS');

      // Content
      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $htmlBody;
      $mail->AltBody = $textBody ?: strip_tags($htmlBody);

      // Kirim email
      $mail->send();

      // Log sukses
      error_log("[PHPMailer] Email sent to: $toEmail");
      return true;
    } catch (Exception $e) {
      // Log error
      $error = isset($mail) ? $mail->ErrorInfo : $e->getMessage();
      error_log("[PHPMailer] Failed to $toEmail: $error");
      return false;
    }
  }

  /**
   * Helper: Get base URL
   */
  private static function getBaseUrl()
  {
    // Sesuaikan dengan konfigurasi CMS Anda
    return 'http://localhost/cms_ivss'; // Ganti dengan BASE_URL Anda
  }

  /**
   * HTML Template untuk approval
   */
  private static function getApprovalHtml($name, $password, $loginUrl)
  {
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #28a745; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; background: #f8f9fa; border-radius: 0 0 5px 5px; }
        .password-box { font-family: 'Courier New', monospace; font-size: 18px; background: #fff3cd; padding: 12px; border-radius: 5px; text-align: center; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Pendaftaran Disetujui!</h2>
        <p>IVSS Laboratory - Politeknik Negeri Malang</p>
    </div>
    <div class="content">
        <h3>Halo, $name!</h3>
        <p>Selamat! Pendaftaran Anda sebagai member <strong>IVSS Laboratory</strong> telah disetujui.</p>
        
        <p><strong>Password sementara Anda:</strong></p>
        <div class="password-box">$password</div>
        <p><small>🔐 Password bersifat sementara, harap ubah setelah login pertama.</small></p>
        
        <p>Silakan login ke sistem:</p>
        <a href="$loginUrl" style="background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;">🚀 Login ke Sistem</a>
        
        <p style="margin-top: 20px;">Jika ada kendala, hubungi admin laboratorium.</p>
        
        <p>Salam,<br><strong>Tim IVSS Laboratory</strong></p>
    </div>
</body>
</html>
HTML;
  }

  /**
   * Text template untuk approval
   */
  private static function getApprovalText($name, $password, $loginUrl)
  {
    return "Halo $name,\n\n"
      . "Pendaftaran Anda sebagai member IVSS Laboratory telah disetujui!\n\n"
      . "INFORMASI AKUN:\n"
      . "Password sementara: $password\n\n"
      . "Silakan login di: $loginUrl\n\n"
      . "⚠️ PENTING: Password bersifat sementara. Ubah setelah login pertama.\n\n"
      . "Salam,\nTim IVSS Laboratory";
  }

  /**
   * HTML Template untuk rejection
   */
  private static function getRejectionHtml($name)
  {
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; background: #f8f9fa; border-radius: 0 0 5px 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Status Pendaftaran</h2>
        <p>IVSS Laboratory</p>
    </div>
    <div class="content">
        <h3>Halo, $name!</h3>
        <p>Terima kasih telah mengajukan pendaftaran sebagai member <strong>IVSS Laboratory</strong>.</p>
        <p>Setelah melalui proses review, kami dengan berat hati harus menginformasikan bahwa pendaftaran Anda <strong>tidak dapat disetujui</strong> untuk saat ini.</p>
        <p>Untuk informasi lebih lanjut, silakan hubungi admin laboratorium.</p>
        <p>Terima kasih atas minat Anda.</p>
        <p>Salam,<br><strong>Tim IVSS Laboratory</strong></p>
    </div>
</body>
</html>
HTML;
  }

  /**
   * Text template untuk rejection
   */
  private static function getRejectionText($name)
  {
    return "Halo $name,\n\n"
      . "Terima kasih telah mendaftar di IVSS Laboratory.\n\n"
      . "Pendaftaran Anda tidak dapat disetujui saat ini.\n\n"
      . "Untuk informasi lebih lanjut, hubungi admin laboratorium.\n\n"
      . "Terima kasih,\nTim IVSS Laboratory";
  }
}
