<?php
// Se incluye la clase del modelo.
require_once('../../models/data/marcas_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $marca = new MarcasData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como trabajador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idTrabajador'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un trabajador ha iniciado sesión.
        switch ($_GET['action']) {
                //BUSCAR
                case 'readPorcentajeZapatosMarca':
                    if ($result['dataset'] = $marca->readPorcentajeZapatosMarca()) {
                        $result['status'] = 1;
                    } else {
                        $result['error'] = 'No existen marcas registradas';
                    }
                    break;
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $marca->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
                //CREAR
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$marca->setNombreMarca($_POST['nombreMarca']) or
                    !$marca->setDescripcionMarca($_POST['descripcionMarca']) or
                    !$marca->setFotoMarca($_FILES['customFile2'])
                ) {
                    $result['error'] = $marca->getDataError();
                } elseif ($marca->createRow()) {
                    $result['status'] = 1;
                    $result['fileStatus'] = Validator::saveFile($_FILES['customFile2'], $marca::RUTA_IMAGEN);
                    $result['message'] = 'Marca creada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear la marca';
                }
                break;
                //BUSCAR TODO
            case 'readAll':
                if ($result['dataset'] = $marca->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen marcas registradas';
                }
                break;
                //BUSCAR UNO
            case 'readOne':
                if (!$marca->setId($_POST['IdMarca'])) {
                    $result['error'] = $marca->getDataError();
                } elseif ($result['dataset'] = $marca->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Marca inexistente';
                }
                break;
                //ACTUALIZAR
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$marca->setId($_POST['id_marca']) or
                    !$marca->setFilename() or
                    !$marca->setNombreMarca($_POST['nombreMarcaD']) or
                    !$marca->setDescripcionMarca($_POST['descripcionMarcaD']) or
                    !$marca->setFotoMarca($_FILES['customFile1'], $marca->getFilename())
                ) {
                    $result['error'] = $marca->getDataError();
                } elseif ($marca->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Marca modificada correctamente';
                    // Se asigna el estado del archivo después de actualizar.
                    $result['fileStatus'] = Validator::changeFile($_FILES['customFile1'], $marca::RUTA_IMAGEN, $marca->getFilename());
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar la marca';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        print(json_encode('Acceso denegado'));
    }
    // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
    $result['exception'] = Database::getException();
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('Content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
}
