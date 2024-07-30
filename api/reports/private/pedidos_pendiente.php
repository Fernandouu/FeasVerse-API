<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;
// Se incluyen las clases para la transferencia y acceso a datos.
require_once('../../models/data/pedidos_data.php');
// Se instancian las entidades correspondientes.
$Pedidos = new PedidosData;

// Se verifica si hay zapatos con esa marca existente, de lo contrario se muestra un mensaje.
if ($dataOrders = $Pedidos->searchOrders('Pendiente', '')) {
    session_start();
    // Se inicia el reporte con el encabezado del documento.
    $pdf->startReport('Reporte FeasVerse de los pedidos en pendiente: ', 'Reporte sobre todos pedidos en pendiente', '', 92, 20,28);
    // Se establece un color de relleno para mostrar el nombre de la categoría.
    $pdf->setFillColor(240);
    // Se establece la fuente para los datos de los productos.
    $pdf->setFont('Arial', '', 11);
    // Se imprimen las celdas con los encabezados.
    
    $pdf->SetTextColor(255, 255, 255); // Color de texto blanco (RGB)
    $pdf->SetFillColor(20, 106, 147); // Establecer color de fondo rojo (RGB)
    $pdf->cell(43.75, 10, 'Nombre', 1, 0, 'C', 1);
    $pdf->cell(43.75, 10, 'Cantidad', 1, 0, 'C', 1);
    $pdf->cell(43.75, 10, 'Precio unitario ($$)', 1, 0, 'C', 1);
    $pdf->cell(43.75, 10, 'Precio Total ($$)', 1, 1, 'C', 1);
    
    $pdf->SetFillColor(255, 255, 255); // Restablecer el color de fondo a blanco (opcional)
    $pdf->SetTextColor(0, 0, 0); 
    // Se recorren los registros fila por fila.
    foreach ($dataOrders as $rowOrders) {
        // Se imprime una celda con el nombre de la categoría.
        $pdf->SetTextColor(255, 255, 255); // Color de texto blanco (RGB)
        $pdf->SetFillColor(14, 114, 161);
        $pdf->cell(175, 10, $pdf->encodeString('Número de pedido: ' . $rowOrders['id_pedido_cliente']), 1, 1, 'C', 1);
        $pdf->cell(175, 10, $pdf->encodeString('Nombre del cliente: ' . $rowOrders['nombre_cliente']), 1, 1, 'C', 1);
        $pdf->SetFillColor(255, 255, 255); // Restablecer el color de fondo a blanco (opcional)
        $pdf->SetTextColor(0, 0, 0); 
        // Se establece la categoría para obtener sus productos, de lo contrario se imprime un mensaje de error.
        if ($Pedidos->setIdPedidoCliente($rowOrders['id_pedido_cliente'])) {
            // Se verifica si existen registros para mostrar, de lo contrario se imprime un mensaje.
            if ($dataProductos = $Pedidos->readShoesOfOrders()) {
                // Se recorren los registros fila por fila.
                foreach ($dataProductos as $rowProducto) {
                    // Se imprimen las celdas con los datos de los productos.
                    $pdf->cell(43.75, 10, $pdf->encodeString($rowProducto['nombre_zapato']), 1, 0);
                    $pdf->cell(43.75, 10, $pdf->encodeString($rowProducto['cantidad_pedido']), 1, 0);
                    $pdf->cell(43.75, 10, '$' . $pdf->encodeString($rowProducto['precio_unitario_zapato']), 1, 0);
                    $pdf->cell(43.75, 10, '$' . $pdf->encodeString($rowProducto['precio_total']), 1, 1);
                }
                $pdf->SetFillColor(227, 227, 227);
                $pdf->cell(175, 10, $pdf->encodeString('El total es: $' . $rowOrders['total_cobrar']), 1, 1, 'R', 1);
            } else {
                $pdf->cell(0, 10, $pdf->encodeString('No hay productos del pedido'), 1, 1);
            }
        } else {
            $pdf->cell(0, 10, $pdf->encodeString('Pedido incorrecta o inexistente'), 1, 1);
        }
    }


    // Se llama implícitamente al método footer() y se envía el documento al navegador web.
    $pdf->output('I', 'pedido.pdf');
} else {
    print('Pedido inexistente');
}
