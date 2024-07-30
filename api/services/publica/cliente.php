<?php
// Se incluyen las clases del modelo y de servicios.
require_once('../../models/data/clientes_data.php');
require_once('../../services/mandar_correo.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $cliente = new ClienteData;
    $mandarCorreo = new mandarCorreo;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'error' => null, 'exception' => null, 'username' => null, 'codigo' => null);

    // Se verifica si existe una sesión iniciada como cliente para realizar las acciones correspondientes.
    if (isset($_SESSION['idCliente'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un cliente ha iniciado sesión.
        switch ($_GET['action']) {
                //OBTENER USUARIO
            case 'getUser':
                if (isset($_SESSION['nombreCliente'])) {
                    $result['status'] = 1;
                    $result['username'] = $_SESSION['nombreCliente'];
                } else {
                    $result['error'] = 'Nombre indefinido';
                }
                break;
                //CERRAR SESION
            case 'logOut':
                if (session_destroy()) {
                    $result['status'] = 1;
                    $result['message'] = 'Sesión eliminada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al cerrar la sesión';
                }
                break;
                //LEER CLIENTE
            case 'readCliente':
                if ($result['dataset'] = $cliente->readCliente()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Cliente inexistente';
                }
                break;
            case 'readCantidadPedidosPorMes':
                if ($result['dataset'] = $cliente->readCantidadPedidosPorMes()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Pedidos inexistentes';
                }
            break;
                //CAMBIAR CONTRASEÑA
            case 'changePassword':
                $_POST = Validator::validateForm($_POST);
                if (!$cliente->checkPassword($_POST['contraActual'])) {
                    $result['error'] = 'Contraseña actual incorrecta';
                } elseif ($_POST['newContra'] != $_POST['confirContra']) {
                    $result['error'] = 'Confirmación de contraseña diferente';
                } elseif (!$cliente->setClave($_POST['newContra'])) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->changePassword()) {
                    $result['status'] = 1;
                    $result['message'] = 'Contraseña cambiada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al cambiar la contraseña';
                }
                break;
                //EDITAR PERFIL
            case 'editProfile':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$cliente->setId($_POST['idCliente']) or
                    !$cliente->setNombre($_POST['nombreInput']) or
                    !$cliente->setApellido($_POST['apellidosInput']) or
                    !$cliente->setCorreo($_POST['correoInput']) or
                    !$cliente->setDUI($_POST['duiInput']) or
                    !$cliente->setTelefono($_POST['telefonoInput']) or
                    !$cliente->setNacimiento($_POST['fechanInput']) or
                    !$cliente->setDireccion($_POST['direccion'])
                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->editProfile()) {
                    $result['status'] = 1;
                    $result['message'] = 'Usuario modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar su usuario';
                }
                break;

            default:
                $result['error'] = 'Acción no disponible dentro de la sesión1';
        }
    } else {
        // Se compara la acción a realizar cuando el cliente no ha iniciado sesión.
        switch ($_GET['action']) {
                //INICIAR SESION
            case 'signUp':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$cliente->setNombre($_POST['nombreInput']) or
                    !$cliente->setApellido($_POST['apellidosInput']) or
                    !$cliente->setCorreo($_POST['correoInput']) or
                    !$cliente->setDUI($_POST['duiInput']) or
                    !$cliente->setTelefono($_POST['telefonoInput']) or
                    !$cliente->setNacimiento($_POST['fechanInput']) or
                    !$cliente->setFechaRegistro($_POST['fecharInput']) or
                    !$cliente->setDireccion($_POST['direccionInput']) or
                    !$cliente->setClave($_POST['contraInput'])
                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($_POST['contraInput'] != $_POST['confirmContraseña']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($cliente->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cuenta registrada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al registrar la cuenta';
                }
                break;
                //BUSCAR MAIL
            case 'searchMail':
                $_POST = Validator::validateForm($_POST);
                if (!$cliente->setPasswordCorreo($_POST['correo_electronico_paso1'])) {
                    $result['error'] = 'Correo electrónico incorrecto';
                } elseif ($result['dataset'] = $cliente->checkMail()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Correo electrónico inexistente';
                }
                break;
                //ENVIAR CODIGO DE RECUPERACION
            case 'enviarCodigoRecuperacion':
                // Generar un código de recuperación
                $codigoRecuperacion = $mandarCorreo->generarCodigoRecuperacion();
                // Preparar el cuerpo del correo electrónico
                $correoDestino = $_POST['correo_electronico_paso1'];
                $nombreDestinatario =  $_POST['nombre_destinatario']; // Puedes personalizar este valor si lo necesitas
                $asunto = 'Código de recuperación';
                // Enviar el correo electrónico y verificar si hubo algún error
                $envioExitoso = $mandarCorreo->enviarCorreoPassword($correoDestino, $nombreDestinatario, $asunto, $codigoRecuperacion);
                if ($envioExitoso === true) {
                    $result['status'] = 1;
                    $result['codigo'] = $codigoRecuperacion;
                    $result['message'] = 'Código de recuperación enviado correctamente';
                } else {
                    $result['status'] = 0;
                    $result['error'] = 'Error al enviar el correo: ' . $envioExitoso;
                }
                break;
                //CAMBIAR CONTRASEÑA LOGIN
            case 'changePasswordLogin':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$cliente->setClave($_POST['claveCliente']) or
                    !$cliente->setId($_POST['idCliente'])
                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($_POST['claveCliente'] != $_POST['confirmarCliente']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($cliente->updatePassword()) {
                    $result['status'] = 1;
                    $result['message'] = 'Se ha actualizado correctamente la contraseña';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el la contraseña';
                }
                break;
                //INICIAR SESION
            case 'logIn':
                $_POST = Validator::validateForm($_POST);
                if (!$cliente->checkUser($_POST['correo'], $_POST['clave'])) {
                    $result['error'] = 'Datos incorrectos';
                } elseif ($cliente->checkStatus()) {
                    $result['status'] = 1;
                    $result['message'] = 'Autenticación correcta';
                } else {
                    $result['error'] = 'La cuenta ha sido desactivada';
                }
                break;

            default:
                $result['error'] = 'Acción no disponible fuera de la sesión2';
        }
    }

    // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
    $result['exception'] = Database::getException();
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('Content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    // Si no se envió una acción válida, se devuelve un mensaje de recurso no disponible.
    print(json_encode('Recurso no disponible'));
}
