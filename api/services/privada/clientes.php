<?php
// Se incluye la clase del modelo.
require_once('../../models/data/clientes_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $cliente = new ClienteData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idTrabajador'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            //BUSCADOR
            case 'searchRows':
                // Verificar si el valor de búsqueda es válido
                if (!Validator::validateSearch($_POST['search'])) {
                    // Si no es válido, se asigna un mensaje de error
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $cliente->searchRows()) {
                    // Si la búsqueda es válida y se encuentran resultados, se establece el estado como éxito y se crea un mensaje
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    // Si la búsqueda es válida pero no se encuentran resultados, se asigna un mensaje de error
                    $result['error'] = 'No hay coincidencias';
                }
                break;
                case 'readAllActivos':
                    if ($result['dataset'] = $trabajador->readAllActivos()) {
                        $result['status'] = 1;
                        $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                    } else {
                        $result['error'] = 'No existen clientes registrados';
                    }
                    break;
                    case 'readAllInactivos':
                        if ($result['dataset'] = $trabajador->readAllInactivos()) {
                            $result['status'] = 1;
                            $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                        } else {
                            $result['error'] = 'No existen clientes registrados';
                        }
                        break;
                case 'readPorcentajeClientes':
                    if ($result['dataset'] = $cliente->readPorcentajeClientes()) {
                        $result['status'] = 1;
                    } else {
                        $result['error'] = 'No existen clientes';
                    }
                    break;
            //CREAR
            case 'createRow':
                // Validar y procesar los datos del formulario para crear un nuevo registro
                $_POST = Validator::validateForm($_POST);
                // Verificar si todos los datos necesarios son válidos
                if (
                    !$cliente->setNombre($_POST['nombreCliente']) or
                    !$cliente->setApellido($_POST['apellidoCliente']) or
                    !$cliente->setCorreo($_POST['correoCliente']) or
                    !$cliente->setTelefono($_POST['telefonoCliente']) or
                    !$cliente->setDUI($_POST['duiCliente']) or
                    !$cliente->setFechaRegistro($_POST['fechaRegistro']) or
                    !$cliente->setNacimiento($_POST['fechaNacimiento']) or
                    !$cliente->setDireccion($_POST['direccionCliente']) or
                    !$cliente->setClave($_POST['claveCliente']) or
                    !$cliente->setEstado($_POST['estadoCliente'])
                ) {
                    // Si algún dato no es válido, se asigna un mensaje de error
                    $result['error'] = $cliente->getDataError();
                } elseif ($_POST['claveCliente'] != $_POST['confirmarCliente']) {
                    // Si las contraseñas no coinciden, se asigna un mensaje de error
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($cliente->createRow()) {
                    // Si se crea el registro correctamente, se establece el estado como éxito y se crea un mensaje
                    $result['status'] = 1;
                    $result['message'] = 'Cliente creado correctamente';
                } else {
                    // Si ocurre un problema al crear el cliente, se asigna un mensaje de error
                    $result['error'] = 'Ocurrió un problema al crear el cliente';
                }
                break;
            //LEER TODOS
            case 'readAll':
                if ($result['dataset'] = $cliente->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen clientes registrados';
                }
                break;
            //LEER UNO
            case 'readOne':
                if (!$cliente->setId($_POST['idCliente'])) {
                    $result['error'] = 'Cliente incorrecto';
                } elseif ($result['dataset'] = $cliente->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Clientes inexistente';
                }
                break;
            //ACTUALIZAR
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$cliente->setId($_POST['idCliente']) or
                    !$cliente->setNombre($_POST['nombreCliente']) or
                    !$cliente->setApellido($_POST['apellidosCliente']) or
                    !$cliente->setCorreo($_POST['correoCliente']) or
                    !$cliente->setTelefono($_POST['telefonoCliente']) or
                    !$cliente->setDUI($_POST['duiCliente']) or
                    !$cliente->setNacimiento($_POST['fechaDeNacimientoCliente']) or
                    !$cliente->setEstado($_POST['estadoCliente'])
                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cliente modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el cliente';
                }
                break;
            //ACTUALIZAR CONTRASEÑA
            case 'updatePassword':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$cliente->setClave($_POST['claveCliente']) or
                    !$cliente->setId($_POST['idCliente'])
                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($_POST['claveCliente'] != $_POST['confirmarCliente']) {
                    $result['error'] = 'Contraseñas diferentes';
                }elseif ($cliente->updatePassword()) {
                    $result['status'] = 1;
                    $result['message'] = 'Se ha actualizado correctamente la contraseña';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el la contraseña';
                }
                break;
            //CAMBIAR EsTATUS DEL CLIENTE
            case 'updateStatus':
                if (
                    !$cliente->setEstado($_POST['estadoCliente']) or
                    !$cliente->setId($_POST['idCliente'])
                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->updateStatus()) {
                    $result['status'] = 1;
                    $result['message'] = 'Estado del cliente modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el estado del cliente';
                }
                break;
            // ELIMINAR
            case 'deleteRow':
                if (!$cliente->setId($_POST['idCliente'])) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cliente eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el cliente';
                }
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