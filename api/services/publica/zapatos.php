<?php
// Se incluye la clase del modelo.
require_once ('../../models/data/zapatos_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    session_start(); // Inicia la sesión.
    // Se instancia la clase correspondiente.
    $zapato = new ZapatosData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'username' => null);

    // Verifica si el usuario ha iniciado sesión.
    if (isset($_SESSION['idCliente'])) {
        $result['session'] = 1; // Indica que hay una sesión activa.
        switch ($_GET['action']) {
            case 'readResumeAllZapatosMarca':
                // Validar los datos del formulario.
                $_POST = Validator::validateForm($_POST);
                // Verificar si el ID de la marca es válido.
                if (!$zapato->setIdMarca($_POST['idMarca'])) {
                    // Si el dato no es válido, se asigna un mensaje de error.
                    $result['error'] = $zapato->getDataError();
                } elseif ($result['dataset'] = $zapato->readResumeAllZapatosMarca()) {
                    // Si los datos son válidos, se obtienen los zapatos y se asigna el mensaje correspondiente.
                    $result['status'] = 1;
                } else {
                    // Si no existen registros, se asigna un mensaje de error.
                    $result['error'] = 'No existen zapatos registrados';
                }
                break;

            case 'searchValue':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $zapato->searchValue()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;

            case 'searchValueZapatoMarca':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $zapato->searchValueZapatoMarca()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;

            case 'searchDetalle':
                // Validar los datos y buscar detalles del zapato.
                if (
                    !$zapato->setIdTalla($_POST['id_talla']) or
                    !$zapato->setIdColor($_POST['id_color']) or
                    !$zapato->setId($_POST['id_zapato'])
                ) {
                    $result['error'] = $zapato->getDataError();
                } elseif ($result['dataset'] = $zapato->searchDetalle()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Zapato inexistente';
                }
                break;
            case 'validationCantidad':
                // Validar los datos y verificar la cantidad disponible del zapato.
                if (
                    !$zapato->setIdDetalleZapato($_POST['id_detalle_zapato'])
                ) {
                    $result['error'] = $zapato->getDataError();
                } elseif ($result['dataset'] = $zapato->validationCantidad()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Zapato inexistente';
                }
                break;
            case 'readAllReciente':
                // Leer los zapatos más recientes.
                if ($result['dataset'] = $zapato->readResumeReciente()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen zapatos registrados';
                }
                break;
            case 'readAllEspecial':
                // Leer los zapatos especiales.
                if ($result['dataset'] = $zapato->readResumeEspecial()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen zapatos registrados';
                }
                break;
            case 'readResumeAllZapatos':
                // Leer los zapatos especiales.
                if ($result['dataset'] = $zapato->readResumeAllZapatos()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen zapatos registrados';
                }
                break;
            case 'readOneDetail':
                // Leer el detalle de un zapato específico.
                if (!$zapato->setId($_POST['id_zapato'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif ($result['dataset'] = $zapato->readOneDetail()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Zapato inexistente';
                }
                break;
            case 'readOneColoresZapato':
                // Leer los colores disponibles de un zapato específico.
                if (!$zapato->setId($_POST['id_zapato'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif ($result['dataset'] = $zapato->readOneColoresZapato()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Zapato inexistente';
                }
                break;
            case 'readOneTallas':
                // Leer las tallas disponibles de un zapato específico.
                if (!$zapato->setId($_POST['id_zapato'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif ($result['dataset'] = $zapato->readOneTallas()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Zapato inexistente';
                }
                break;
            case 'readOneReseñas':
                // Leer las reseñas de un zapato específico.
                if (!$zapato->setId($_POST['id_zapato'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif ($result['dataset'] = $zapato->readOneResegnas()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Zapato inexistente';
                }
                break;
            case 'readColoresDisponiblesForTalla':
                if (!$zapato->setId($_POST['id_zapato'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif (!$zapato->setIdTalla($_POST['id_talla'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif ($result['dataset'] = $zapato->readColoresDisponiblesForTalla()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Colores no disponibles para esta talla';
                }
                break;
            case 'readTallasDisponiblesForColor':
                if (!$zapato->setId($_POST['id_zapato'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif (!$zapato->setIdColor($_POST['id_color'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif ($result['dataset'] = $zapato->readTallasDisponiblesForColor()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Tallas no disponibles para ese color';
                }
                break;
            case 'readMasVendido':
                // Leer los zapatos más recientes.
                if ($result['dataset'] = $zapato->readMasVendido()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen zapatos registrados';
                }
                break;
            default:
                // Si no se reconoce la acción, se asigna un mensaje de error.
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        // Se compara la acción a realizar cuando no hay una sesión iniciada.
        switch ($_GET['action']) {
            case 'readMasVendido':
                // Leer los zapatos más recientes.
                if ($result['dataset'] = $zapato->readMasVendido()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen zapatos registrados';
                }
                break;
            case 'readAllReciente':
                // Leer los zapatos más recientes.
                if ($result['dataset'] = $zapato->readResumeReciente()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen zapatos registrados';
                }
                break;
            case 'readTallasDisponiblesForColor':
                if (!$zapato->setId($_POST['id_zapato'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif (!$zapato->setIdColor($_POST['id_color'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif ($result['dataset'] = $zapato->readTallasDisponiblesForColor()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Tallas no disponibles para ese color';
                }
                break;
            case 'readColoresDisponiblesForTalla':
                if (!$zapato->setId($_POST['id_zapato'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif (!$zapato->setIdTalla($_POST['id_talla'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif ($result['dataset'] = $zapato->readColoresDisponiblesForTalla()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Colores no disponibles para esta talla';
                }
                break;

            case 'readAllEspecial':
                // Leer los zapatos especiales.
                if ($result['dataset'] = $zapato->readResumeEspecial()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen zapatos registrados';
                }
                break;
            case 'readResumeAllZapatos':
                // Leer los zapatos especiales.
                if ($result['dataset'] = $zapato->readResumeAllZapatos()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen zapatos registrados';
                }
                break;
            case 'readOneDetail':
                // Leer el detalle de un zapato específico.
                if (!$zapato->setId($_POST['id_zapato'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif ($result['dataset'] = $zapato->readOneDetail()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Zapato inexistente';
                }
                break;
            case 'readOneColoresZapato':
                // Leer los colores disponibles de un zapato específico.
                if (!$zapato->setId($_POST['id_zapato'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif ($result['dataset'] = $zapato->readOneColoresZapato()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Zapato inexistente';
                }
                break;
            case 'readOneTallas':
                // Leer las tallas disponibles de un zapato específico.
                if (!$zapato->setId($_POST['id_zapato'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif ($result['dataset'] = $zapato->readOneTallas()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Zapato inexistente';
                }
                break;
            case 'readOneReseñas':
                // Leer las reseñas de un zapato específico.
                if (!$zapato->setId($_POST['id_zapato'])) {
                    $result['error'] = $zapato->getDataError();
                } elseif ($result['dataset'] = $zapato->readOneResegnas()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Zapato inexistente';
                }
                break;
            default:
                // Si no se reconoce la acción, se asigna un mensaje de error.
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
    }
    // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
    $result['exception'] = Database::getException();
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('Content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print (json_encode($result));
} else {
    // Si no se envió una acción válida, se devuelve un mensaje de recurso no disponible.
    print (json_encode('Recurso no disponible'));
}
