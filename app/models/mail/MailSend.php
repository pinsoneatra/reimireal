<?php

namespace models\mail;

use models\system\Database;
use models\system\Systema;

class MailSend {

    private $system;
    private $conn;

    public function __construct() {
        $this->conn = new Database();
        $this->system = new Systema();
    }

    public function sendMail($to,$subject,$email,$txt) {
            $headers = "From: <{$email}> \r\n";
           // $headers .= "Cc:  \r\n";
            $headers .= "MIME-Version: 1.0 \r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";

            mail($to, $subject, $txt, $headers);
    }

    public function sendMailBulk() {
        if (isset($_POST['sendmailbulk'])) {
            $allowedExtensions = array("pdf", "doc", "docx", "gif", "jpeg", "jpg", "png", "rtf", "txt");
            $files = array();
            foreach ($_FILES as $name => $file) {
                $file_name = $file['name'];
                $temp_name = $file['tmp_name'];
                $file_type = $file['type'];
                $path_parts = pathinfo($file_name);
                $ext = $path_parts['extension'];
                if (!in_array($ext, $allowedExtensions)) {
                    die("File $file_name has the extensions $ext which is not allowed");
                }
                array_push($files, $file);
            }

            // email fields: to, from, subject, and so on
            $to = 'reimi846@gmail.com';
            $from = $this->system->myescape($_POST['email']);
            $subject = 'Job';
            $message = 'Apply CV';
            $headers = "From: $from";

            // boundary
            $semi_rand = md5(time());
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

            // headers for attachment
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

            // multipart boundary
            $message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
            $message .= "--{$mime_boundary}\n";

            // preparing attachments
            for ($x = 0; $x < count($files); $x++) {
                $file = fopen($files[$x]['tmp_name'], "rb");
                $data = fread($file, filesize($files[$x]['tmp_name']));
                fclose($file);
                $data = chunk_split(base64_encode($data));
                $name = $files[$x]['name'];
                $message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$name\"\n" .
                        "Content-Disposition: attachment;\n" . " filename=\"$name\"\n" .
                        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
                $message .= "--{$mime_boundary}\n";
            }
            // send
            //$schedule = $this->system->myescape($_POST['settime']);
            $ok = mail($to, $subject, $message, $headers);
            if ($ok) {
                echo "<p>mail sent to $to!</p>";
            } else {
                echo "<p>mail could not be sent!</p>";
            }
        }
    }

}

?>
