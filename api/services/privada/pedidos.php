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
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idTrabajador'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
                //BUSCAR ORDENES
            case 'searchOrders':
                $_POST = Validator::validateForm($_POST);
                if ($result['dataset'] = $pedidos->searchOrders($_POST['estado'], $_POST['paramentro'])) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen pedidos con esa búsqueda';
                }
                break;
            case 'createRow':
                break;
                //BUSCAR ORDEnES
            case 'readAllOrders':
                if ($result['dataset'] = $pedidos->readAllOrders()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen pedidos registrados';
                }
                break;
                //GRAFICO DE PEDIDOS
            case 'graficoPedidos':
                if ($result['dataset'] = $pedidos->graficaPedidos()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen pedidos registrados';
                }
                break;
                //LEER TODO LOS ZAPATOS DE UNA ORDEN
            case 'ReadAllShoesOfOneOrder':
                if (!$pedidos->setIdPedidoCliente($_POST['idPedido'])) {
                    $result['error'] = $pedidos->getDataError();
                } elseif ($result['dataset'] = $pedidos->readShoesOfOrders()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Zapatos inexistente';
                }
                break;
                //LEEER TODA LOS REPARTIdORES
            case 'readAllOrdersDeliverys':
                if ($result['dataset'] = $pedidos->readAllOrdersWorkers()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen repartidores registrados';
                }
                break;
                //BUSCAR REPARTIDOREs
            case 'searchOrdersWorkers':
                if ($result['dataset'] = $pedidos->searchOrdersWorkers($_POST['paramentro'])) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen repartidores con esa búqueda';
                }
                break;
                //LEER ORDENES DE REPARTIDORES
            case 'ReadOrderOfDeliverys':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$pedidos->setIdRepartidor($_POST['idTrabajador']) or
                    !$pedidos->setEstadoPedido2($_POST['estado'])
                ) {
                    $result['error'] = $pedidos->getDataError();
                } elseif ($result['dataset'] = $pedidos->readOrdersOfWorkerCategories()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Pedidos inexistente';
                }
                break;
                //ACTUALIZAR ESTADO
            case 'updateStatus':
                $_POST = Validator::validateForm($_POST);
                if (!$pedidos->setIdPedidoCliente($_POST['idPedido']) or !$pedidos->setEstadoPedido($_POST['estado'])) {
                    $result['error'] = $pedidos->getDataError();
                } elseif ($result['dataset'] = $pedidos->updateStatus()) {
                    $result['status'] = 1;
                    $result['message'] = 'Se ha actualizado correctamente el estado';
                } else {
                    $result['error'] = 'No se pudo cambiar el estado del pedido';
                }
                break;
                //VENTA POR MES
            case 'ventasPorMes':
                if ($result['dataset'] = $pedidos->ventasMes()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay datos disponibles';
                }
                break;
            case 'deleteRow':
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
        print(json_encode('Acceso denegado'));
    }
} else {
    print(json_encode('Recurso no disponible'));
}
