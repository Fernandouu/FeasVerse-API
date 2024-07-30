<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;

// Se inicia la sesión
session_start();

// Se incluyen las clases para la transferencia y acceso a datos.
require_once('../../models/data/clientes_data.php');

// Se instancian las entidades correspondientes.
$cliente = new ClienteData;

// Se verifica si hay trabajadores existentes, de lo contrario se muestra un mensaje.
if ($dataCliente = $cliente->readAllActivos()) {
    
    // Se inicia el reporte con el encabezado del documento.
    $pdf->startReport('Clientes activos de', 'Reporte sobre todos los clientes inactivos de nuestra tienda', 'FeasVerse', 12, 59, 20);
    // Se establece un color de relleno para los encabezados.
    $pdf->setFillColor(225);
    // Se establece la fuente para los encabezados.
    $pdf->setFont('Arial', 'B', 12);
    // Se imprimen las celdas con los encabezados.
    $pdf->Cell(10);
    $pdf->SetTextColor(255, 255, 255); // Color de texto blanco (RGB)
    $pdf->SetFillColor(20, 106, 147); // Establecer color de fondo rojo (RGB)
    $pdf->Cell(100, 10, 'Nombre', 0, 0, 'C', 1); // 'C' para centrar y '1' para dibujar el borde
    $pdf->Cell(30, 10, 'Apellido', 0, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Estado', 0, 1, 'C', 1); // 1 para dibujar el borde y 1 para nueva línea
    $pdf->SetFillColor(255, 255, 255); // Restablecer el color de fondo a blanco (opcional)
    $pdf->SetTextColor(0, 0, 0); 
    // Se establece la fuente para los datos de los productos.
    $pdf->setFont('Arial', '', 11);
    // Se recorren los registros fila por fila.
    foreach ($dataCliente as $rowCliente) {
        $pdf->cell(10);
        
        // Imprimir las celdas con los datos del trabajador
        $pdf->cell(100, 10, $pdf->encodeString($rowCliente['nombre_cliente']), 1, 0);
        $pdf->cell(30, 10, $pdf->encodeString($rowCliente['apellido_cliente']), 1, 0, 'C');
        $pdf->cell(30, 10, $pdf->encodeString($rowCliente['estado_cliente']), 1, 1, 'C');
    }
    
    
} else {
    $pdf->cell(0, 10, $pdf->encodeString('No hay clientes activos'), 1, 1);
}

// Se llama implícitamente al método footer() y se envía el documento al navegador web.
$pdf->output('I', 'clientes_activos.pdf');
?>
