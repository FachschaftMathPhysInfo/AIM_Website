<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
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

        function send_confirm_mail($contact_mail, array $arr) {
            $message = "Sehr geehrte Damen und Herren,\n\nvielen Dank für Ihre Anmeldung zur fAIM 2020/21. Ihre Daten wurden erfolgreich in unsere Datenbank eingetragen. Weitere Informationen senden wir Ihnen in Kürze.\n\nMit freundlichen Grüßen,\nDie Fachschaft MathPhysInfo";
            $headers = "From: Fachschaft MathPhysInfo - fAIM <aim@mathphys.stura.uni-heidelberg.de>\r\n";
            //$headers .= "BCC: aim@mathphys.stura.uni-heidelberg.de\r\n";
            $headers .= "Reply-To: aim@mathphys.stura.uni-heidelberg.de\r\n";
            $headers .= "Organization: Fachschaft MathPhysInfo\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/plain; charset=UTF-8\r\n";
            $headers .= "X-Mailer: PHP". phpversion() ."\r\n";
            $subj = "Bestätigung Ihrer fAIM-Anmeldung";
            $subject = '=?utf-8?Q?' . quoted_printable_encode($subj) . '?=';
            if ( mail($contact_mail,$subject,$message,$headers) ) {
                echo "Sent Mail successfully";
            }
            else {
                echo "<br />Fehler beim Senden der Bestätigungsmail. Ansonsten sollte aber alles funktioniert haben.";
            }
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

        // **File upload**
        $target_dir = "uploads/";
        $orig_file = basename( $_FILES["fileToUpload"]["name"]);
        $imageFileType = strtolower(pathinfo($orig_file,PATHINFO_EXTENSION));
        $target_file = $target_dir . "cover_image_" . $group_id . "." . $imageFileType;
        $uploadOk = 1;
        $uploadSuccess = 0;

        if (empty($_FILES["fileToUpload"]["name"]) || $_FILES["fileToUpload"]["size"] == 0) {
            // No file to upload
            echo "No file provided. You can still send it to <a href='mailto:fachschaft@mathphys.stura.uni-heidelberg.de'>fachschaft@mathphys.stura.uni-heidelberg.de</a> later.";
        } else {
            // Check if file already exists
            if (file_exists($target_file)) {
                echo "Error: File already exists.";
                $uploadOk = 0;
            }
            
            // Check file size
            if ($_FILES["fileToUpload"]["size"] > 50000000) {
                echo "Error: File is too large (> 50 MB).";
                $uploadOk = 0;
            }

            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded. Please submit the form again";
            } else {
                // Upload
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
                    $uploadSuccess=1;
                } else {
                    echo "Sorry, there was an error uploading your file.";
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
                echo "Error: Database file count not be written. Please try again.";
            } else {
                send_confirm_mail($contact, $arr);
            }
            
            //echo '<pre>'; print_r($json); echo '</pre>';
        }

        ?>
    </div>
</div>
</div>
</body>
</html>