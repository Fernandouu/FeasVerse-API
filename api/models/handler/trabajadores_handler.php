<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class TrabajadorHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id_trabajador = null; // ID del trabajador
    protected $nombre_trabajador = null; // Nombre del trabajador
    protected $apellido_trabajador = null; // Apellido del trabajador
    protected $dui_trabajador = null; // DUI del trabajador
    protected $telefono_trabajador = null; // Teléfono del trabajador
    protected $correo_trabajador = null; // Correo del trabajador
    protected $clave_trabajador = null; // Clave del trabajador (hash)
    protected $fecha_de_registro = null; // Fecha de registro del trabajador
    protected $fecha_de_nacimiento = null; // Fecha de nacimiento del trabajador
    protected $id_nivel = null; // ID del nivel del trabajador
    protected $estado_trabajador = null; // Estado del trabajador (activo/inactivo)

    /*
     *  Métodos para gestionar la cuenta del administrador.
     */

    // Método para verificar el usuario del trabajador
    public function checkUser($mail, $password)
    {
        $sql = "SELECT id_trabajador, nombre_trabajador, clave_trabajador
                FROM tb_trabajadores
                WHERE  correo_trabajador = ? AND estado_trabajador = 'Activo' AND id_nivel = 1;"; // Consulta SQL para obtener datos del trabajador por correo electrónico
        $params = array($mail); // Parámetros para la consulta SQL
        $data = Database::getRow($sql, $params); // Ejecución de la consulta SQL
        // Verificación de existencia de datos y coincidencia de contraseña
        if (!($data = Database::getRow($sql, $params))) {
            return false; // Si no hay datos, retorna falso
        } elseif (password_verify($password, $data['clave_trabajador'])) {
            $_SESSION['idTrabajador'] = $data['id_trabajador']; // Se guarda el ID del trabajador en la sesión
            $_SESSION['nombreTrabajador'] = $data['nombre_trabajador']; // Se guarda el nombre del trabajador en la sesión
            return true; // Retorna verdadero si las credenciales son correctas
        } else {
            return false; // Retorna falso si las credenciales son incorrectas
        }
    }

    // Método para verificar si el correo del trabajador ya existe en la base de datos
    public function checkMail()
    {
        $sql = 'SELECT id_trabajador, nombre_trabajador, correo_trabajador
                FROM tb_trabajadores
                WHERE  correo_trabajador = ?'; // Consulta SQL para verificar correo existente
        $params = array($this->correo_trabajador); // Parámetros para la consulta SQL
        return Database::getRow($sql, $params); // Ejecución de la consulta SQL
    }

    // Método para verificar la contraseña actual del trabajador
    public function checkPassword($password)
    {
        $sql = 'SELECT clave_trabajador
                FROM tb_trabajadores
                WHERE id_trabajador = ?'; // Consulta SQL para obtener la contraseña del trabajador por ID
        $params = array($_SESSION['idTrabajador']); // Parámetros para la consulta SQL
        $data = Database::getRow($sql, $params); // Ejecución de la consulta SQL
        // Verificación de coincidencia de contraseñas
        if (password_verify($password, $data['clave_trabajador'])) {
            return true; // Retorna verdadero si la contraseña es correcta
        } else {
            return false; // Retorna falso si la contraseña es incorrecta
        }
    }

    // Método para cambiar la contraseña del trabajador
    public function changePassword()
    {
        $sql = 'UPDATE tb_trabajadores
        SET clave_trabajador = ?
        WHERE id_trabajador = ?'; // Consulta SQL para actualizar la contraseña del trabajador
        $params = array($this->clave_trabajador, $_SESSION['idTrabajador']); // Parámetros para la consulta SQL
        return Database::executeRow($sql, $params); // Ejecución de la consulta SQL
    }

    // Método para actualizar la contraseña del trabajador por ID
    public function updatePassword()
    {
        $sql = 'UPDATE tb_trabajadores
                SET clave_trabajador = ?
                WHERE id_trabajador = ?'; // Consulta SQL para actualizar la contraseña del trabajador por ID
        $params = array($this->clave_trabajador, $this->id_trabajador); // Parámetros para la consulta SQL
        return Database::executeRow($sql, $params); // Ejecución de la consulta SQL
    }

    // Método para leer todos los trabajadores
    public function readAll()
    {
        $sql = 'SELECT t.id_trabajador, t.apellido_trabajador, t.nombre_trabajador, t.dui_trabajador, t.telefono_trabajador, t.correo_trabajador, n.nivel, t.estado_trabajador FROM tb_trabajadores t
        INNER JOIN tb_niveles n WHERE n.id_nivel = t.id_nivel AND id_trabajador != ? ORDER BY t.estado_trabajador ASC;'; // Consulta SQL para obtener todos los trabajadores
        $params = array($_SESSION['idTrabajador']); // Parámetros para la consulta SQL
        return Database::getRows($sql, $params);
    }

    public function readAllInactivos(){
        $sql = 'SELECT t.id_trabajador, t.apellido_trabajador, t.nombre_trabajador, t.dui_trabajador, t.telefono_trabajador, t.correo_trabajador, n.nivel, t.estado_trabajador
FROM tb_trabajadores t
INNER JOIN tb_niveles n ON n.id_nivel = t.id_nivel
WHERE t.id_trabajador != ? AND t.estado_trabajador = "Desactivo"
ORDER BY t.apellido_trabajador'; // Consulta SQL para obtener todos los trabajadores
        $params = array($_SESSION['idTrabajador']); // Parámetros para la consulta SQL
        return Database::getRows($sql, $params);
    }

    public function readAllActivos(){
        $sql = 'SELECT t.id_trabajador, t.apellido_trabajador, t.nombre_trabajador, t.dui_trabajador, t.telefono_trabajador, t.correo_trabajador, n.nivel, t.estado_trabajador
FROM tb_trabajadores t
INNER JOIN tb_niveles n ON n.id_nivel = t.id_nivel
WHERE t.id_trabajador != ? AND t.estado_trabajador = "Activo"
ORDER BY t.apellido_trabajador'; // Consulta SQL para obtener todos los trabajadores
        $params = array($_SESSION['idTrabajador']); // Parámetros para la consulta SQL
        return Database::getRows($sql, $params);
    }



    // Método para crear un nuevo trabajador
    public function createRow()
    {
        $sql = 'INSERT INTO  tb_trabajadores(nombre_trabajador, apellido_trabajador, dui_trabajador, telefono_trabajador, correo_trabajador, clave_trabajador, fecha_de_registro, fecha_de_nacimiento, id_nivel, estado_trabajador)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);'; // Consulta SQL para insertar un nuevo trabajador
        $params = array(
            $this->nombre_trabajador,
            $this->apellido_trabajador,
            $this->dui_trabajador,
            $this->telefono_trabajador,
            $this->correo_trabajador,
            $this->clave_trabajador,
            $this->fecha_de_registro,
            $this->fecha_de_nacimiento,
            $this->id_nivel,
            $this->estado_trabajador
        ); // Parámetros para la consulta SQL
        return Database::executeRow($sql, $params); // Ejecución de la consulta SQL
    }

    // Método para actualizar los datos de un trabajador por ID
    public function updateRow()
    {
        $sql = 'UPDATE tb_trabajadores SET 
        nombre_trabajador = ?,
        apellido_trabajador = ?,
        dui_trabajador = ?,
        telefono_trabajador = ?,
        correo_trabajador = ?,
        fecha_de_nacimiento = ?,
        id_nivel = ?,
        estado_trabajador = ? WHERE id_trabajador = ?;'; // Consulta SQL para actualizar los datos de un trabajador por ID
        $params = array(
            $this->nombre_trabajador,
            $this->apellido_trabajador,
            $this->dui_trabajador,
            $this->telefono_trabajador,
            $this->correo_trabajador,
            $this->fecha_de_nacimiento,
            $this->id_nivel,
            $this->estado_trabajador,
            $this->id_trabajador
        ); // Parámetros para la consulta SQL
        return Database::executeRow($sql, $params); // Ejecución de la consulta SQL
    }

    // Método para editar el perfil del trabajador
    public function editProfile()
    {
        $sql = 'UPDATE tb_trabajadores SET 
        nombre_trabajador = ?,
        apellido_trabajador = ?,
        dui_trabajador = ?,
        telefono_trabajador = ?,
        correo_trabajador = ?,
        fecha_de_nacimiento = ? WHERE id_trabajador = ?;'; // Consulta SQL para editar el perfil del trabajador por ID
        $params = array(
            $this->nombre_trabajador,
            $this->apellido_trabajador,
            $this->dui_trabajador,
            $this->telefono_trabajador,
            $this->correo_trabajador,
            $this->fecha_de_nacimiento,
            $_SESSION['idTrabajador']
        ); // Parámetros para la consulta SQL
        return Database::executeRow($sql, $params); // Ejecución de la consulta SQL
    }

    // Método para leer todos los niveles
    public function readNiveles()
    {
        $sql = 'SELECT id_nivel, nivel from tb_niveles; '; // Consulta SQL para obtener todos los niveles
        return Database::getRows($sql); // Ejecución de la consulta SQL
    }

    // Método para verificar duplicados por valor (DUI o correo) y excluyendo el ID actual
    public function checkDuplicate($value)
    {
        $sql = 'SELECT id_trabajador 
        FROM tb_trabajadores 
        WHERE (dui_trabajador = ? OR correo_trabajador = ?)
        AND id_trabajador <> ?;'; // Consulta SQL para verificar duplicados por valor (DUI o correo) excluyendo el ID actual
        $params = array($value, $value, $this->id_trabajador); // Parámetros para la consulta SQL
        return Database::getRow($sql, $params); // Ejecución de la consulta SQL
    }

    // Método para leer los datos de un trabajador por ID
    public function readOne()
    {
        $sql = 'SELECT id_trabajador, nombre_trabajador, apellido_trabajador, dui_trabajador, telefono_trabajador, correo_trabajador, clave_trabajador, fecha_de_registro, fecha_de_nacimiento, id_nivel, estado_trabajador
        FROM tb_trabajadores WHERE id_trabajador = ?;'; // Consulta SQL para obtener los datos de un trabajador por ID
        $params = array($this->id_trabajador); // Parámetros para la consulta SQL
        return Database::getRow($sql, $params); // Ejecución de la consulta SQL
    }

    // Método para leer los datos del administrador actual (logueado)
    public function readAdmin()
    {
        $sql = 'SELECT id_trabajador, nombre_trabajador, apellido_trabajador, dui_trabajador, telefono_trabajador, correo_trabajador, clave_trabajador, fecha_de_registro, fecha_de_nacimiento, id_nivel, estado_trabajador
        FROM tb_trabajadores WHERE id_trabajador = ?;'; // Consulta SQL para obtener los datos del administrador actual (logueado)
        $params = array($_SESSION['idTrabajador']); // Parámetros para la consulta SQL
        return Database::getRow($sql, $params); // Ejecución de la consulta SQL
    }

    // Método para bloquear o desbloquear un trabajador por ID
    public function bloqDesbloqRow()
    {
        $sql = 'UPDATE tb_trabajadores SET
        estado_trabajador = ?
        WHERE id_trabajador = ?;'; // Consulta SQL para bloquear o desbloquear un trabajador por ID
        $params = array($this->estado_trabajador, $this->id_trabajador); // Parámetros para la consulta SQL
        return Database::executeRow($sql, $params); // Ejecución de la consulta SQL
    }
}
