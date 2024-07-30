<?php
// Se incluye la clase del modelo.
require_once('../../models/data/pedidos_data.php');
require_once('../../services/mandar_factura.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $pedidos = new PedidosData;
    $mandarCorreo = new mandarCorreoFactura;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como cliente, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idCliente'])) {
        $result['session'] = 1; // Indica que hay una sesión activa.
        // Se compara la acción a realizar cuando un cliente ha iniciado sesión.
        switch ($_GET['action']) {
                // Crear un detalle de pedido
            case 'createDetallePedido':
                // Validar y procesar los datos del formulario para crear un nuevo registro
                $_POST = Validator::validateForm($_POST);
                // Verificar si todos los datos necesarios son válidos
                if (
                    !$pedidos->setIdDetalleZapato($_POST['id_detalle_zapato']) or
                    !$pedidos->setCantidadPedido($_POST['cantidad_pedido']) or
                    !$pedidos->setPrecioDelZapato($_POST['precio_del_zapato'])
                ) {
                    // Si algún dato no es válido, se asigna un mensaje de error
                    $result['error'] = $pedidos->getDataError();
                } elseif ($result['dataset'] = $pedidos->createDetallePedido()) {
                    $result['status'] = 1;
                    $result['message'] = 'Se ha creado correctamente el detalle pedido';
                } else {
                    $result['error'] = 'ERROR no se pudo agregar';
                }
                break;

                // Buscar órdenes
            case 'searchOrders':
                $_POST = Validator::validateForm($_POST);
                if ($result['dataset'] = $pedidos->SearchOrdersClients($_POST['estado'])) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen pedidos con esa búsqueda';
                }
                break;

                // Leer todas las órdenes de clientes
            case 'readAllOrdersOfClients':
                if ($result['dataset'] = $pedidos->readAllOrdersClients()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen pedidos registrados';
                }
                break;

                // Comentarios
            case 'comentario':
                if ($result['dataset'] = $pedidos->comentario()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'Ya comentaste a los productos comprados';
                }
                break;

                // Crear un comentario
            case 'comentarioCreate':
                // Validar y procesar los datos del formulario para crear un nuevo comentario
                $_POST = Validator::validateForm($_POST);
                // Verificar si todos los datos necesarios son válidos
                if (
                    !$pedidos->setIdDetallesPedido($_POST['idDetalleZapato']) or
                    !$pedidos->setTituloComentario($_POST['tituloComentario']) or
                    !$pedidos->setDescripcionComentario($_POST['descripcionComentario']) or
                    !$pedidos->setFechaComentario($_POST['fecha']) or
                    !$pedidos->setCalificacionComentario($_POST['calificacion']) or
                    !$pedidos->setEstadoComentario($_POST['estado'])
                ) {
                    // Si algún dato no es válido, se asigna un mensaje de error
                    $result['error'] = $pedidos->getDataError();
                } elseif ($result['dataset'] = $pedidos->createComentario()) {
                    $result['status'] = 1;
                    $result['message'] = 'Se ha creado correctamente el comentario';
                } else {
                    $result['error'] = 'Ya comentaste a los productos comprados';
                }
                break;

                // Leer todos los zapatos de una orden específica
            case 'ReadAllShoesOfOneOrder':
                if (!$pedidos->setIdPedidoCliente($_POST['idPedido'])) {
                    $result['error'] = $pedidos->getDataError();
                } elseif ($result['dataset'] = $pedidos->readShoesOfOrders()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Zapatos inexistente';
                }
                break;
            case 'enviarFactura':
                // Preparar el cuerpo del correo electrónico
                $correoDestino = $_SESSION['correoCliente'];
                $nombreDestinatario =  $_SESSION['nombreCliente']; // Puedes personalizar este valor si lo necesitas
                $asunto = 'Correo de factura';
                $rutaPDF = $_POST['rutaPDF'];
                // Enviar el correo electrónico y verificar si hubo algún error
                $envioExitoso = $mandarCorreo->enviarCorreoFactura($correoDestino, $nombreDestinatario, $asunto, $rutaPDF);
                if ($envioExitoso === true) {
                    $result['status'] = 1;
                    $result['message'] = 'Factura enviada con exito';
                } else {
                    $result['status'] = 0;
                    $result['error'] = 'Error al enviar el correo: ' . $envioExitoso;
                }
                print(json_encode($result));
                break;


            default:
                // Si no se reconoce la acción, se asigna un mensaje de error
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
        // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
        $result['exception'] = Database::getException();
        // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
        header('Content-type: application/json; charset=utf-8');
        // Se imprime el resultado en formato JSON y se retorna al controlador.
        print(json_encode($result));
    } else {
        // Si no hay una sesión iniciada, se devuelve un mensaje de acceso denegado.
        print(json_encode('Acceso denegado'));
    }
} else {
    // Si no se envió una acción válida, se devuelve un mensaje de recurso no disponible.
    print(json_encode('Recurso no disponible'));
}
