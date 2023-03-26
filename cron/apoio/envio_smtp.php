<?PHP
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

$id_log = $_POST["id_log"];
$mensagem_enviar = $_POST["mensagem_enviar"];
$mensagem_enviar_html = $_POST["mensagem_enviar_html"];
$tipo = $_POST["tipo"];

//$id_log = 15;
//$mensagem_enviar = "teste";
//$mensagem_enviar_html = "teste";

if($id_log && $mensagem_enviar && $mensagem_enviar_html) {
    $db = mysqli_connect("localhost", "root", "#H0gGLS3@XeaW702_i51z@yUlN#", "Ravi");
    $resSystem = mysqli_query($db, "SELECT userSMTP, senhaSMTP, servidorSMTP, portaSMTP, emailSMTP, SMTPtls FROM system;");
    $SysAlertas = mysqli_fetch_array($resSystem);

    $mail = new PHPMailer;
    $mail->isSMTP();
    if($SysAlertas['SMTPtls'] == 1) {
        $mail->SMTPSecure = 'tls';
    }
    $mail->SMTPDebug = 0;
    $mail->Host = $SysAlertas['servidorSMTP'];
    $mail->Port = $SysAlertas['portaSMTP'];
    $mail->SMTPAuth = true;
    $mail->Username = $SysAlertas['userSMTP'];
    $mail->Password = $SysAlertas['senhaSMTP'];
    $mail->setFrom($SysAlertas['userSMTP'], 'Ravi Monitor');
    $mail->addReplyTo($SysAlertas['userSMTP'], 'Ravi Monitor');
    $mail->addAddress($SysAlertas['emailSMTP']);
    $mail->Subject = 'Problema resolvido - Ravi Monitoramento';
    $mail->msgHTML(utf8_decode($mensagem_enviar_html));
    $mail->AltBody = $mensagem_enviar;

    if(!$mail->send()) {
        mysqli_query($db, "UPDATE Logalertas SET enviadoSMTP = '3' WHERE id = $id_log;");
    }else {
        if($tipo == 1) {
            mysqli_query($db, "UPDATE Logalertas SET enviadoSMTP = '1' WHERE id = $id_log;");
        }else {
            mysqli_query($db, "UPDATE Logalertas SET enviadoSMTP = '2' WHERE id = $id_log;");
        }
    }
    mysqli_close($db);
}

exit(0);
?>