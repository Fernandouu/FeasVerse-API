<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se inicia la sesión
session_start();

// Se incluyen las clases para la transferencia y acceso a datos.
require_once('../../models/data/clientes_data.php');

// Se instancian las entidades correspondientes.
$cliente = new ClienteData();

// Verificar si hay trabajadores existentes, de lo contrario se muestra un mensaje.
if ($dataCliente = $cliente->readAllInactivos()) {
    // Se instancia la clase para crear el reporte.
    $pdf = new Report();
    
    // Se inicia el reporte con el encabezado del documento.
    $pdf->startReport('Clientes inactivos de', 'Reporte sobre todos los clientes inactivos de nuestra tienda', 'FeasVerse', 13, 59, 24);
    
    // Se establece un color de relleno para los encabezados.
    $pdf->setFillColor(20, 106, 147); // Color de fondo azul oscuro (RGB)
    $pdf->setTextColor(255, 255, 255); // Color de texto blanco (RGB)
    
    // Se establece la fuente para los encabezados.
    $pdf->setFont('Arial', 'B', 12);
    
    // Se imprimen las celdas con los encabezados.
    $pdf->Cell(10); // Espacio en blanco
    $pdf->Cell(100, 10, 'Nombre', 1, 0, 'C', true); // 'C' para centrar y true para dibujar el borde
    $pdf->Cell(30, 10, 'Apellido', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Estado', 1, 1, 'C', true); // 1 para dibujar el borde y 1 para nueva línea
    
    // Restablecer color de fondo a blanco (opcional) y color de texto a negro
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0, 0, 0);
    
    // Se establece la fuente para los datos de los productos.
    $pdf->setFont('Arial', '', 11);
    
    // Se recorren los registros fila por fila.
    foreach ($dataCliente as $rowCliente) {
        $pdf->Cell(10); // Espacio en blanco
        
        // Imprimir las celdas con los datos del cliente
        $pdf->Cell(100, 10, $pdf->encodeString($rowCliente['nombre_cliente']), 1, 0);
        $pdf->Cell(30, 10, $pdf->encodeString($rowCliente['apellido_cliente']), 1, 0, 'C');
        $pdf->Cell(30, 10, $rowCliente['estado_cliente'], 1, 1, 'C');
    }
    
} else {
    // Mensaje cuando no hay datos
    $pdf = new Report(); // Instanciar nuevamente el objeto PDF
    $pdf->startReport('Clientes inactivos de', 'Reporte sobre todos los clientes inactivos de nuestra tienda', 'FeasVerse', 13, 59, 24);
    $pdf->Cell(0, 10, $pdf->encodeString('No hay clientes inactivos'), 1, 1, 'C');
}

// Se llama al método output() para enviar el documento al navegador web.
$pdf->output('I', 'clientes_inactivos.pdf');
?>
