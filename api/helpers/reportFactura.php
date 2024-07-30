<?php
require_once('../../libraries/fpdf185/fpdf.php');

class ReportFactura extends FPDF
{
    // URL actual
    const CURRENT_URL = 'http://localhost/FeasVerse-Web/vistas/publico';
    // Variables para el título del reporte
    private $title = null;
    private $minLetter = null;
    private $letterUnderline = null;
    private $userName = null;
    private $regular = null;
    private $black = null;
    private $regular2 = null;
    private $anchoDemas = null;
    private $espaciado = null;
    private $celdaNegritaW = null;

    private $direccionUsuario = null;
    private $telefonoUsuario = null;
    private $correoUsuario = null;
    private $duiUsuario = null;
    private $apellidoUsuario = null;

    // Método para iniciar el reporte
    public function startReport(
        $title,
        $minLetter,
        $letterUnderline,
        $anchoDemas,
        $espaciado,
        $celdaNegritaW = 35,
        $nameSession = 'idTrabajador',
        $direccionUsuario,
        $telefonoUsuario,
        $correoUsuario,
        $duiUsuario,
        $apellidoUsuario
    ) {
        // Se verifica si hay una sesión activa
        if (isset($_SESSION[$nameSession])) {
            $this->title = $title;
            $this->minLetter = $minLetter;
            $this->letterUnderline = $letterUnderline;
            $this->anchoDemas = $anchoDemas;
            $this->espaciado = $espaciado;
            $this->celdaNegritaW = $celdaNegritaW;

            $this->direccionUsuario = $direccionUsuario;
            $this->telefonoUsuario = $telefonoUsuario;
            $this->correoUsuario = $correoUsuario;
            $this->duiUsuario = $duiUsuario;
            $this->apellidoUsuario = $apellidoUsuario;

            // Se establece el nombre de usuario
            if ($nameSession == 'idTrabajador')
                $this->userName = $_SESSION['nombreTrabajador'];
            else if ($nameSession == 'idCliente')
                $this->userName = $_SESSION['nombreCliente'];

            // Dividir el título en tres partes
            $titleParts = explode(' ', $title, 3);
            if (count($titleParts) >= 3) {
                $this->regular = $titleParts[0];     // Zapatos
                $this->black = $titleParts[1];       // FEASVERSE
                $this->regular2 = $titleParts[2];    // de la marca
            }

            // Se establece el título del documento
            $this->setTitle('FeasVerse - Reporte', true);
            // Se establece el margen del documento
            $this->setMargins(15, 0, 15); // 15, 0, 15 para ajustar el margen de la página donde el 15 es el margen izquierdo, 0 es el margen superior y 15 es el margen derecho
            //Se añade una página y la orientaccion y el tamaño de la hoja
            $this->addPage('P', 'Letter');
            // Se establece el encabezado y pie de página
            $this->aliasNbPages();
            $this->onlyFrstPage();
        } else {
            // Si no hay una sesión activa, se redirige al inicio de sesión
            header('location:' . self::CURRENT_URL);
        }
    }

    // Método para codificar una cadena
    public function encodeString($string)
    {
        // Convertir la cadena a ISO-8859-1
        return mb_convert_encoding($string, 'ISO-8859-1', 'utf-8');
    }

    // Método para el encabezado del documento
    public function header()
    {
        // Se establece el logo.
        $this->putImages();
        // Se establece el logo.
        $this->ln(3); // Salto de línea
        $this->addText(0, $this->minLetter, 11, [91, 91, 91], ''); // 91, 91, 91 para el color gris, el 11 es el tamaño de la letra y 0 es el ancho de la celda
        $this->ln(5);
    }

    // Método para mostrar el contenido del documento
    public function onlyFrstPage()
    {
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/FeasVerse-Api/api/helpers/images/FeasVerseLogo.png';
        $this->image($imagePath, 95, 13, 17); // 95, 13, 17 para ajustar la imagen al tamaño de la página donde 93 es el ancho y 13 es el alto y 17 es el tamaño de la imagen
        $this->ln(23);
        $this->addText(0, 'Fecha/Hora: ' . date('d-m-Y H:i:s'), 12, [0, 0, 0], 'I', 'C'); // 'I' para cursiva y 'C' para centrar
        $this->ln(2);

        $this->Cell($this->espaciado);
        // Agregar las partes del título con diferentes estilos y control de línea
        $this->setFont('Arial', '', 18);
        $this->Cell(30, 10, $this->encodeString($this->regular), 0, 0, 'C', 0); // 'C' para centrar y '1' para dibujar el borde
        $this->setFont('Arial', 'B', 18); // 'B' para negrita
        $this->Cell($this->celdaNegritaW, 10, $this->encodeString($this->black), 0, 0, 'C', 0);
        $this->setFont('Arial', '', 18);
        $this->Cell($this->anchoDemas, 10, $this->encodeString($this->regular2), 0, 1, 'C', 0); // 1 para nueva línea 
        $this->Cell(45);

        $this->ln(2);
        $this->addText(0, $this->letterUnderline, 22, [0, 0, 0], 'U', 'C'); // 'U' para subrayar y 'C' para centrar 
        $this->ln(9);
        $this->addText(0, 'Datos del cliente: ', 15, [0, 0, 0], '', 'L');
        $this->ln(4);
        $this->addText(0, 'Nombre del cliente: ' . $this->userName, 12, [0, 0, 0], '', 'L'); // 'L' para alinear a la izquierda
        $this->addText(0, 'Apellido del cliente: ' . $this->apellidoUsuario, 12, [0, 0, 0], '', 'L');
        $this->addText(0, 'DUI del cliente: ' . $this->duiUsuario, 12, [0, 0, 0], '', 'L');
        $this->addText(0, 'Dirección del cliente: ' . $this->direccionUsuario, 12, [0, 0, 0], '', 'L');
        $this->addText(0, 'Teléfono del cliente: ' . $this->telefonoUsuario, 12, [0, 0, 0], '', 'L');
        $this->addText(0, 'Correo del cliente: ' . $this->correoUsuario, 12, [0, 0, 0], '', 'L');
        $this->ln(5);
        // Se agrega un salto de línea para mostrar el contenido principal del documento.
    }

    // Método para las imagenes de los bordes
    private function putImages()
    {
        $imagePathLeft = $_SERVER['DOCUMENT_ROOT'] . '/FeasVerse-Api/api/helpers/images/BorderLeft.png';
        $imagePathRight = $_SERVER['DOCUMENT_ROOT'] . '/FeasVerse-Api/api/helpers/images/BorderRight.png';
        if (file_exists($imagePathLeft) && file_exists($imagePathRight)) {
            $this->Image($imagePathLeft, 0, 0, 0, 0); // 0, 0, 0, 0 para ajustar la imagen al tamaño de la página
            $this->Image($imagePathRight, 200, 0, 0, 0); // 200, 0, 0, 0 para ajustar la imagen al tamaño de la página
        } else {
            throw new Exception("No se encontró la imagen en la ruta especificada: $imagePathLeft");
        }
    }

    // Método para el pie de página del documento
    public function footer()
    {
        // Posición: a 1,5 cm del final
        $this->setY(-15);
        // Establecer la fuente
        $this->setFont('Arial', 'I', 8);
        // Número de página
        $this->cell(0, 10, $this->encodeString('Página ') . $this->pageNo() . '/{nb}', 0, 0, 'C');
    }

    // Método para agregar texto al documento
    public function addText($w, $text, $size = 12, $color = [0, 0, 0], $style = '', $align = 'L', $nextInline = true)
    {
        // Establecer fuente y color de texto
        $this->setFont('Arial', $style, $size);
        $this->setTextColor($color[0], $color[1], $color[2]);

        // Ajustar alineación
        $alignments = ['L' => 'L', 'C' => 'C', 'R' => 'R'];
        $alignment = isset($alignments[$align]) ? $alignments[$align] : 'L';

        // Imprimir texto condicion de salto de línea
        if ($nextInline) {
            // Imprimir en la misma línea
            $this->cell($w, 5, $this->encodeString($text), 0, 0, $alignment, 0);
        } else {
            // Imprimir con salto de línea
            $this->multiCell($w, 5, $this->encodeString($text), 0, $alignment);
        }

        $this->ln(5); // Salto de línea después del texto
    }
}
