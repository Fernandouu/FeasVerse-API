<?php
// Se incluye la clase del modelo.
require_once('../../models/data/zapatos_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se instancia la clase correspondiente.
    $zapato = new ZapatosData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'username' => null);

    // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
    switch ($_GET['action']) {
            // Caso para leer todos los zapatos de una marca específica.
        case 'readAllZapatoMarca':
            // Validar los datos del formulario.
            $_POST = Validator::validateForm($_POST);
            // Verificar si el ID de la marca es válido.
            if (!$zapato->setIdMarca($_POST['idMarca'])) {
                // Si el dato no es válido, se asigna un mensaje de error.
                $result['error'] = $pedidos->getDataError();
            } elseif ($result['dataset'] = $zapato->readAllZapatoMarca()) {
                // Si los datos son válidos, se obtienen los zapatos y se asigna el mensaje correspondiente.
                $result['status'] = 1;
                $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
            } else {
                // Si no existen registros, se asigna un mensaje de error.
                $result['error'] = 'No existen zapatos registrados';
            }
            break;

            // Caso para leer todas las tallas disponibles.
        case 'readTallas':
            if ($result['dataset'] = $zapato->readTallas()) {
                $result['status'] = 1;
                $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
            } else {
                $result['error'] = 'No existen zapatos registrados';
            }
            break;

            // Caso para leer todos los colores disponibles.
        case 'readColor':
            if ($result['dataset'] = $zapato->readColoresPublic()) {
                $result['status'] = 1;
                $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
            } else {
                $result['error'] = 'No existen zapatos registrados';
            }
            break;

            // Caso para buscar zapatos por marca, nombre, talla y color.
        case 'searchZapatoMarca':
            // Verificar si el ID de la marca es válido.
            if (!$zapato->setIdMarca($_POST['idMarca'])) {
                $result['error'] = $zapato->getDataError();
            } else {
                // Obtener los valores de búsqueda si están definidos.
                $nombreZapato = isset($_POST['value']) ? $_POST['value'] : '';
                $tallas = isset($_POST['tallas']) ? $_POST['tallas'] : [];
                $idColor = isset($_POST['coloresSelect']) ? $_POST['coloresSelect'] : null;

                // Configurar los valores en la instancia del zapato.
                $zapato->setNombreZapato($nombreZapato);
                $zapato->setIdColor($idColor);

                // Buscar zapatos con los criterios definidos.
                if ($result['dataset'] = $zapato->searchZapatoMarca($tallas)) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen zapatos registrados';
                }
            }
            break;

            // Caso por defecto cuando la acción no está definida.
        default:
            $result['error'] = 'Acción no disponible dentro de la sesión';
    }

    // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
    $result['exception'] = Database::getException();
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('Content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    // Si no hay una acción definida, se devuelve un mensaje de recurso no disponible.
    print(json_encode('Recurso no disponible'));
}
