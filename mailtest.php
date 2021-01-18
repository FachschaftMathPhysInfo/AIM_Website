<html>
<body>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$mail = new PHPMailer(TRUE);
$group_code = 'ABC';
$arr = ["name"=>'A', "field"=>'UP', "contact"=>'mail@mail.de', "times"=>[0,1,1,1,2], "total_times"=>2];

try {
    $mail->IsSMTP();
    $mail->Encoding = 'base64';
    $mail->CharSet = 'UTF-8';
    $mail->Host = 'mail.mathphys.stura.uni-heidelberg.de';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 25; 

    $mail->setFrom('aim@mathphys.stura.uni-heidelberg.de', 'Fachschaft MathPhysInfo – fAIM');
    //$mail->addAddress('aim@mathphys.stura.uni-heidelberg.de', 'Fachschaft MathPhysInfo – fAIM');
    $mail->addAddress('olmehling@yahoo.com', 'Oliver Mehling');
    $mail->Subject = 'fAIM: Neue Anmeldung';
    $mail->isHTML(true);
    $mail->Body = "<p>Neue Anmeldung für die fAIM 2020/21</p><p>Gruppen-ID: " . $group_code . "<br />Zeit: ". date(DateTime::ISO8601) . "</p><p>Weitere Daten:<pre>" . print_r($arr) . "</pre></p>";
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