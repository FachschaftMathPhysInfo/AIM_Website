<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Anmeldung abgeschlossen &middot; fAIM</title>
    <script src="jquery-3.5.1.min.js"></script>
    <link rel="icon" type="image/png" href="favicon.png" />
    <link rel="stylesheet" href="bulma-0.9.1.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="page-content">
<!-- Header -->
<div id="header">
    <div id="title" class="aim-title">
        <h2>Anmeldung zur fAIM</h2>
    </div>
    <div id="success-message">
        <?php
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\Exception;
        require 'vendor/autoload.php';

        function getRandomID() {
            //$keys = array_keys($rjson['groups']);
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';
            
            for ($i = 0; $i < 3; $i++) {
                $index = rand(0, strlen($characters) - 1);
                $randomString .= $characters[$index];
            }

            return $randomString;
        }

        function array_first_value(array $a) {
            foreach ($a as $fv) {
                return $fv;
            }
            return null; // return null if array is empty
        }

        function send_confirm_mail($contact_mail, $group_name, $group_code) {
            $sentMail = 0;
            $mail = new PHPMailer(TRUE);
            try {
                $mail->IsSMTP();
                $mail->Encoding = 'base64';
                $mail->CharSet = 'UTF-8';
                $mail->Host = 'mail.mathphys.stura.uni-heidelberg.de';
                //$mail->SMTPDebug  = 0; 
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 25; 

                $mail->addReplyTo('aim@mathphys.stura.uni-heidelberg.de', 'Fachschaft MathPhysInfo – fAIM');
                $mail->setFrom('aim@mathphys.stura.uni-heidelberg.de', 'Fachschaft MathPhysInfo – fAIM');
                $mail->addAddress($contact_mail);
                //$mail->addBCC('aim@mathphys.stura.uni-heidelberg.de');
                $mail->Subject = 'Bestätigung Ihrer fAIM-Anmeldung';
                $mail->Body = "Sehr geehrte Damen und Herren,\n\nvielen Dank für die Anmeldung Ihrer Arbeitsgruppe '" . $group_name . "' zur fAIM 2020/21. Ihre Daten wurden erfolgreich in unsere Datenbank eingetragen. Weitere Informationen senden wir Ihnen in Kürze.\n\nFalls Sie noch Änderungen vornehmen oder ein Bild/Video nachreichen möchten, geben Sie bitte das zufällig generierte Kürzel '" . $group_code . "' mit an.\n\nMit freundlichen Grüßen,\nDie Fachschaft MathPhysInfo";
                $mail->send();
            }
            catch (Exception $e)
            {
                echo $e->errorMessage();
                $sentMail--;
            }
            catch (\Exception $e)
            {
                /* PHP exception */
                echo $e->getMessage();
                $sentMail--;
            }
            if ($sentMail >= 0) {
                echo "<p>Vielen Dank für Ihre Anmeldung! Sie sollten eine Bestätigungsmail erhalten haben. Weitere Informationen senden wir Ihnen in Kürze.</p>";
            }
        }

        function send_orga_mail(arr $arr, $group_code) {
            $mail = new PHPMailer(TRUE);
            try {
                $mail->IsSMTP();
                $mail->Encoding = 'base64';
                $mail->CharSet = 'UTF-8';
                $mail->Host = 'mail.mathphys.stura.uni-heidelberg.de';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 25; 

                $mail->setFrom('aim@mathphys.stura.uni-heidelberg.de', 'Fachschaft MathPhysInfo – fAIM');
                //$mail->addAddress('aim@mathphys.stura.uni-heidelberg.de', 'Fachschaft MathPhysInfo – fAIM');
                $mail->addAddress('mehling@mathphys.stura.uni-heidelberg.de', 'Oliver Mehling');
                $mail->Subject = 'fAIM: Neue Anmeldung';
                $mail->isHTML(true);
                $mail->Body = "<p>Neue Anmeldung für die fAIM 2020/21</p><p>Gruppen-ID: " . $group_code . "<br />Zeit: ". date(DateTime::ISO8601) . "</p><p>Weitere Daten:<pre>" . print_r($arr) . "</pre></p>";
                $mail->send();
            }
            catch (Exception $e) { }
        }

        // **Read in variables**
        $token = $_POST['token'];
        $name = $_POST['name'];
        $field = $_POST['field'];
        $contact = $_POST['contact'];
        $profs = $_POST['profs'];
        $link = $_POST['website'];
        $desc_de = $_POST['desc-de'];
        $desc_en = $_POST['desc-en'];
        $group_id = getRandomID();
        $times = array();
        for ($i=1; $i<=5; $i++) {
            $times[$i-1] = $_POST['time'.$i];
        }
        $total_times=$_POST['nslots'];

        $uploadOk = 1;
        // Check whether something was actually submitted
        if("" == trim($_POST['name'])){
            echo "<p>Bitte füllen Sie das Formular vollständig aus (<a href='form.html'>zurück</a>)</p>";
            $uploadOk=0;
        }

        // **File upload**
        $target_dir = "uploads/";
        $orig_file = basename( $_FILES["fileToUpload"]["name"]);
        $imageFileType = strtolower(pathinfo($orig_file,PATHINFO_EXTENSION));
        $target_file = $target_dir . "cover_image_" . $group_id . "." . $imageFileType;
        $uploadSuccess = 0;

        if ($uploadOk == 1) {
            if (empty($_FILES["fileToUpload"]["name"]) || $_FILES["fileToUpload"]["size"] == 0) {
                // No file to upload
                echo "<p class='upload-message'>Sie haben kein Bild hochgeladen oder der Upload ist fehlgeschlagen. Gerne können Sie die Datei an <a href='mailto:aim@mathphys.stura.uni-heidelberg.de'>aim@mathphys.stura.uni-heidelberg.de</a> nachreichen.</p>";
            } else {
                // Check if file already exists
                if (file_exists($target_file)) {
                    echo "<p>File Upload Error: File already exists.</p>";
                    $uploadOk = 0;
                }
                
                // Check file size
                if ($_FILES["fileToUpload"]["size"] > 50000000) {
                    echo "<p>File Upload Error: File is too large (> 50 MB).</p>";
                    $uploadOk = 0;
                }

                if ($uploadOk == 0) {
                    echo "<p>Sorry, your file was not uploaded. Please submit the form again.</p>";
                } else {
                    // Upload
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        echo "<p class='upload-message'>Die Datei ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " wurde erfolgreich hochgeladen.</p>";
                        $uploadSuccess=1;
                    } else {
                        echo "<p>File Upload Error: Sorry, there was an error uploading your file.</p>";
                    }
                }
            }
        }

        $text = array();
        if (!empty($desc_de)) {
            $text['de'] = $desc_de;
        }
        if (!empty($desc_en)) {
            $text['en'] = $desc_en;
        }
        if (sizeof($text)<2) {
            $text = array_first_value($text);
            if ($text == null) {
                $text = "";
            }
        }

        // Read and write JSON
        $fn = 'groups_stub.json';
        $rjson = json_decode(file_get_contents($fn), true);
        //$keys = array_keys($rjson['groups']);

        $arr = ["name"=>$name, "field"=>$field, "contact"=>$contact, "profs"=>$profs,
        "room"=>sizeof($rjson['groups'])+1, "text"=>$text, "times"=>$times, "total_times"=>$total_times];
        if (!empty($link)) {
            $arr["link"] = $link;
        }
        if ($uploadSuccess == 1) {
            $arr["image"] = $target_file;
        }

        if ($uploadOk == 1) {
            // Write to file if everything went right
            $json = $rjson;
            $json['groups'][$group_id]=$arr;
            if (file_put_contents($fn, json_encode($json, JSON_PRETTY_PRINT)) === FALSE) {
                echo "<p>Error: Database file count not be written. Please try again.</p>";
            } else {
                send_confirm_mail($contact, $name, $group_id);
                send_orga_mail($arr, $group_id);
            }
            
            //echo '<pre>'; print_r($json); echo '</pre>';
        }

        ?>
    </div>
</div>
<div class="is-full" id="textfoot">
    <p>&copy; Fachschaft MathPhysInfo 2021 &middot; <a href="impressum.html">Impressum</a> &middot; <a href="mailto:aim@mathphys.stura.uni-heidelberg.de">Kontakt</a></p>
</div>
</div>
</body>
</html>