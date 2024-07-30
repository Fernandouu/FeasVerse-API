<?php
// Se incluye la clase del modelo.
require_once('../../models/data/pedidos_data.php');
// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $pedidos = new PedidosData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como cliente para realizar las acciones correspondientes.
    if (isset($_SESSION['idCliente'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un cliente ha iniciado sesión.
        switch ($_GET['action']) {
            // CREAR
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                // Verificar si todos los datos necesarios son válidos
                if (
                    !$pedidos->setEstadoPedido2($_POST['estado_pedido'])
                ) {
                    // Si algún dato no es válido, se asigna un mensaje de error
                    $result['error'] = $pedidos->getDataError();
                } elseif ($pedidos->createRowPedidos()) {
                    // Si se crea el registro correctamente, se establece el estado como éxito y se crea un mensaje
                    $result['status'] = 1;
                    $result['message'] = 'Carrito creado correctamente';
                } else {
                    // Si ocurre un problema al crear el pedido, se asigna un mensaje de error
                    $result['error'] = 'Ocurrió un problema al crear el carrito';
                }
                break;
            // LEER TODOS
            case 'readAll':
                if ($result['dataset'] = $pedidos->readShoesOfCarritos()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen carrito del cliente';
                }
                break;
            case 'readAllCarrito':
                if ($result['dataset'] = $pedidos->verCarrito()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen carrito del cliente';
                }
                break;
            // ACTUALIZAR
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                // Verificar si todos los datos necesarios son válidos
                if (
                    !$pedidos->setIdDetallesPedido($_POST['idDetallesPedido']) or
                    !$pedidos->setCantidadPedido($_POST['cantidad']) 
                ) {
                    // Si algún dato no es válido, se asigna un mensaje de error
                    $result['error'] = $pedidos->getDataError();
  
                } elseif ($pedidos->updateRowDetalle()) {
                    // Si se actualiza el registro correctamente, se establece el estado como éxito y se crea un mensaje
                    $result['status'] = 1;
                    $result['message'] = 'Carrito actualizado correctamente';
                } else {
                    // Si ocurre un problema al actualizar el pedido, se asigna un mensaje de error
                    $result['error'] = 'Ocurrió un problema al actualizar el carrito';
                }
                break;
            // CAMBIAR ESTADO DEL PEDIDO
            case 'update':
                $_POST = Validator::validateForm($_POST);
                // Verificar si todos los datos necesarios son válidos
                if (
                    !$pedidos->setIdPedidoCliente($_POST['id_pedido_cliente']) or
                    !$pedidos->setIdRepartidor($_POST['id_repartidor']) or
                    !$pedidos->setEstadoPedido2($_POST['estado_pedido']) or
                    !$pedidos->setPrecioTotal($_POST['precio_total']) or
                    !$pedidos->setFechaDeInicio($_POST['fecha_de_inicio']) or
                    !$pedidos->setIdCostoDeEnvioPorDepartamento($_POST['id_costo_de_envio_por_departamento'])
                ) {
                    // Si algún dato no es válido, se asigna un mensaje de error
                    $result['error'] = $pedidos->getDataError();
                } elseif ($pedidos->updateRowPedidos()) {
                    // Si se actualiza el registro correctamente, se establece el estado como éxito y se crea un mensaje
                    $result['status'] = 1;
                    $result['message'] = 'Estado del pedido actualizado correctamente';
                } else {
                    // Si ocurre un problema al actualizar el estado del pedido, se asigna un mensaje de error
                    $result['error'] = 'Ocurrió un problema al actualizar el estado del pedido';
                }
                break;

            // ELIMINAR
            case 'deleteRow':
                if (!$pedidos->setIdDetallesPedido($_POST['idDetallesPedido'])) {
                    $result['error'] = $pedidos->getDataError();
                } elseif ($pedidos->deleteRowPedidos()) {
                    $result['status'] = 1;
                    $result['message'] = 'Carrito eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el carrito';
                }
                break;
            case 'leerPrecios':
                if ($result['dataset'] = $pedidos->readPrecio()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No hay precios';
                }
                break;
            case 'leerRepartidor':
                if ($result['dataset'] = $pedidos->readRepartidores()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No hay repartidores';
                }
                break;
            default:
                // Si no se reconoce la acción, se asigna un mensaje de error
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
        // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
        $result['exception'] = Database::getException();
        // Se indica el tipo de
        // contenido a mostrar y su respectivo conjunto de caracteres.
        header('Content-type: application/json; charset=utf-8');
        // Se imprime el resultado en formato JSON y se retorna al controlador.
        print(json_encode($result));
    } else {
        // Si no hay sesión iniciada, se devuelve un mensaje de acceso denegado.
        print(json_encode('Acceso denegado'));
    }
} else {
    // Si no se envió una acción válida, se devuelve un mensaje de recurso no disponible.
    print(json_encode('Recurso no disponible'));
}