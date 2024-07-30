<?php
require_once('../../libraries/phpmailer651/src/PHPMailer.php');
require_once('../../libraries/phpmailer651/src/SMTP.php');
require_once('../../libraries/phpmailer651/src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class mandarCorreoFactura
{
    function enviarCorreoFactura($correoDestino, $nombreDestinatario, $asunto, $rutaPDF)
    {
        // Instanciar la clase PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Descargar el archivo PDF
            $pdfContent = file_get_contents($rutaPDF);
            if ($pdfContent === FALSE) {
                throw new Exception('No se pudo acceder al archivo PDF en la URL proporcionada.');
            }
            
            // Guardar el contenido del archivo PDF temporalmente
            $tempPdfPath = tempnam(sys_get_temp_dir(), 'pdf_') . '.pdf';
            file_put_contents($tempPdfPath, $pdfContent);

            // Configuración del servidor SMTP (en este caso, Gmail)
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Servidor SMTP de Gmail
            $mail->SMTPAuth   = true;
            $mail->Username   = 'feasverse@gmail.com'; // Tu dirección de correo electrónico de Gmail
            $mail->Password   = 'kbkg izjr zajz uzvu'; // Tu contraseña de Gmail
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Configuración del correo electrónico
            $mail->setFrom('feasverse@gmail.com', 'FEASVERSE.SV');
            $mail->addAddress($correoDestino, $nombreDestinatario);
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8'; // Establecer la codificación de caracteres
            $mail->Subject = '=?UTF-8?B?' . base64_encode($asunto) . '?='; // Asunto codificado en base64

            // Diseño del cuerpo del correo electrónico
            $cuerpo = '
            <html>
            <head>
                <style>
                    .container {
                        max-width: 600px;
                        margin: auto;
                        padding: 20px;
                        font-family: Arial, sans-serif;
                    }
                    .header {
                        background-color: #1A89BD; /* Color principal */
                        padding: 10px;
                        text-align: center;
                        border-radius: 10px 10px 0 0; /* Redondear bordes superiores */
                    }
                    .content {
                        padding: 20px;
                        background-color: #FFFFFF; /* Fondo blanco */
                        color: #000000; /* Texto negro */
                        border-radius: 0 0 10px 10px; /* Redondear bordes inferiores */
                        border: 1px solid #1A89BD; /* Borde con color principal */
                    }
                    .card {
                        margin-bottom: 20px;
                    }
                    .company-name {
                        color: #FFFFFF; /* Nombre de la empresa en blanco */
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1 class="company-name">FEASVERSE . SV</h1>
                    </div>
                    <div class="content">
                        <div class="card">
                            <p>Hola ' . $nombreDestinatario . ',</p>
                            <p>Recibiste este correo electrónico para comprobante de factura.</p>
                            <p>¡Gracias!</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>
        ';

            $mail->Body = $cuerpo;

            // Adjuntar el archivo PDF
            $mail->addAttachment($tempPdfPath);

            // Enviar correo
            $mail->send();

            // Eliminar el archivo temporal después de enviar el correo
            unlink($tempPdfPath);

            // Devolver verdadero si el correo se envió correctamente
            return true;
        } catch (Exception $e) {
            // Devolver el mensaje de error si hubo un problema
            return $mail->ErrorInfo;
        }
    }
}
?>
