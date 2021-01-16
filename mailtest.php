<html>
<body>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$mail = new PHPMailer(TRUE);

try {
    $mail->IsSMTP();
    $mail->Encoding = 'base64';
    $mail->CharSet = 'UTF-8';
    $mail->Host = 'relays.uni-heidelberg.de';
    //$mail->SMTPDebug  = 0; 
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587; 

    $mail->addReplyTo('aim@mathphys.stura.uni-heidelberg.de', 'Fachschaft MathPhysInfo – fAIM');
    $mail->setFrom('aim@mathphys.stura.uni-heidelberg.de', 'Fachschaft MathPhysInfo – fAIM');
    $mail->addAddress('mehling@mathphys.stura.uni-heidelberg.de');
    //$mail->addBCC('aim@mathphys.stura.uni-heidelberg.de');
    $mail->Subject = 'Bestätigung Ihrer fAIM-Anmeldung';
    $mail->Body = "Sehr geehrte Damen und Herren,\n\nvielen Dank für Ihre Anmeldung zur fAIM 2020/21. Ihre Daten wurden erfolgreich in unsere Datenbank eingetragen. Weitere Informationen senden wir Ihnen in Kürze.\n\nMit freundlichen Grüßen,\nDie Fachschaft MathPhysInfo";
    $mail->send();
}
catch (Exception $e)
{
    echo $e->errorMessage();
}
catch (\Exception $e)
{
    /* PHP exception */
    echo $e->getMessage();
}
?>
</body>
</html>