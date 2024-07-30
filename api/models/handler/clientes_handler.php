<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
*	Clase para manejar el comportamiento de los datos de la tabla CLIENTE.
*/
class ClienteHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    */
    protected $id_cliente = null;
    protected $nombre_cliente = null;
    protected $apellido_cliente = null;
    protected $dui_cliente = null;
    protected $telefono_cliente = null;
    protected $correo_cliente = null;
    protected $direccion_cliente = null;
    protected $clave_cliente = null;
    protected $fecha_de_registro = null;
    protected $fecha_de_nacimiento = null;
    protected $estado_cliente = null;

    /*
    *   Métodos para gestionar la cuenta del cliente.
    */


    public function checkUser($correo_cliente, $clave_cliente)
    {
        // Consulta SQL para buscar un usuario por correo electrónico
        $sql = "SELECT id_cliente, nombre_cliente, correo_cliente, clave_cliente, estado_cliente
            FROM tb_clientes
            WHERE correo_cliente = ? AND estado_cliente = 'Activo';";
        $params = array($correo_cliente);


        // Obtiene los datos del usuario de la base de datos
        $data = Database::getRow($sql, $params);

        // Verifica si la contraseña proporcionada coincide con la contraseña hash almacenada en la base de datos
        if (password_verify($clave_cliente, $data['clave_cliente'])) {
            // Si la contraseña coincide, establece las propiedades de la clase con los datos del usuario y devuelve true
            $this->id_cliente = $data['id_cliente'];
            $_SESSION['idCliente'] = $data['id_cliente'];
            $_SESSION['nombreCliente'] = $data['nombre_cliente'];
            $_SESSION['correoCliente'] = $data['correo_cliente'];
            $this->correo_cliente = $data['correo_cliente'];
            $this->estado_cliente = $data['estado_cliente'];
            return true;
        } else {
            // Si la contraseña no coincide, devuelve false
            return false;
        }
    }

    // Método para leer todas las marcas
    public function readPorcentajeClientes()
    {
        $sql = 'SELECT * FROM vw_total_clientes_por_estado;';
        return Database::getRows($sql);
    }

    public function readAllInactivos()
    {
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, dui_cliente, telefono_cliente, correo_cliente, direccion_cliente, clave_cliente, fecha_de_registro, fecha_de_nacimiento, estado_cliente
                FROM tb_clientes
                WHERE estado_cliente = "Desactivo";'; // Consulta SQL para obtener todos los trabajadores // Parámetros para la consulta SQL
        return Database::getRows($sql);
    }

    public function readAllActivos()
    {
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, dui_cliente, telefono_cliente, correo_cliente, direccion_cliente, clave_cliente, fecha_de_registro, fecha_de_nacimiento, estado_cliente
                FROM tb_clientes
                WHERE estado_cliente = "Activo";'; // Consulta SQL para obtener todos los trabajadores // Parámetros para la consulta SQL
        return Database::getRows($sql);
    }


    public function checkMail()
    {
        $sql = 'SELECT id_cliente, nombre_cliente, correo_cliente
                FROM tb_clientes
                WHERE  correo_cliente = ?'; // Consulta SQL para verificar correo existente
        $params = array($this->correo_cliente); // Parámetros para la consulta SQL
        return Database::getRow($sql, $params); // Ejecución de la consulta SQL
    }


    public function checkStatus()
    {
        // Verifica si el estado del cliente es verdadero (activo)
        if ($this->estado_cliente) {
            // Si está activo, establece las variables de sesión y devuelve true
            $_SESSION['idCliente'] = $this->id_cliente;
            $_SESSION['correo_electronico'] = $this->correo_cliente;
            return true;
        } else {
            // Si no está activo, devuelve false
            return false;
        }
    }

    public function changePassword()
    {
        // Consulta SQL para cambiar la contraseña del cliente
        $sql = 'UPDATE tb_clientes
            SET clave_cliente = ?
            WHERE id_cliente = ?';
        $params = array($this->clave_cliente, $_SESSION['idCliente']);

        // Ejecuta la consulta de actualización de contraseña y devuelve el resultado
        return Database::executeRow($sql, $params);
    }

    public function editProfile()
    {
        // Consulta SQL para editar el perfil del cliente
        $sql = 'UPDATE tb_clientes
            SET nombre_cliente = ?, apellido_cliente = ?, correo_cliente = ?, dui_cliente = ?, telefono_cliente = ?, fecha_de_nacimiento = ?, direccion_cliente = ?
            WHERE id_cliente = ?';
        $params = array($this->nombre_cliente, $this->apellido_cliente, $this->correo_cliente, $this->dui_cliente, $this->telefono_cliente, $this->fecha_de_nacimiento, $this->direccion_cliente, $_SESSION['idCliente']);
        // Ejecuta la consulta de actualización de perfil y devuelve el resultado
        return Database::executeRow($sql, $params);
    }


    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
    */
    public function searchRows()
    {
        // Obtiene el valor de búsqueda del validador y lo formatea para buscar coincidencias parciales en la base de datos
        $value = '%' . Validator::getSearchValue() . '%';

        // Consulta SQL para buscar clientes cuyo apellido, nombre o correo coincidan parcialmente con el valor de búsqueda
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, correo_cliente, dui_cliente, telefono_cliente, fecha_de_nacimiento, fecha_de_registro, direccion_cliente, estado_cliente
            FROM tb_clientes
            WHERE apellido_cliente LIKE ? OR nombre_cliente LIKE ? OR correo_cliente LIKE ?
            ORDER BY id_cliente';

        // Parámetros de la consulta SQL
        $params = array($value, $value, $value);

        // Ejecuta la consulta y devuelve los resultados
        return Database::getRows($sql, $params);
    }

    // Método para verificar la contraseña actual del trabajador
    public function checkPassword($password)
    {
        $sql = 'SELECT clave_cliente
                    FROM tb_clientes
                   WHERE id_cliente = ?'; // Consulta SQL para obtener la contraseña del trabajador por ID
        $params = array($_SESSION['idCliente']); // Parámetros para la consulta SQL
        $data = Database::getRow($sql, $params); // Ejecución de la consulta SQL
        // Verificación de coincidencia de contraseñas
        if (password_verify($password, $data['clave_cliente'])) {
            return true; // Retorna verdadero si la contraseña es correcta
        } else {
            return false; // Retorna falso si la contraseña es incorrecta
        }
    }

    public function createRow()
    {
        // Consulta SQL para insertar un nuevo cliente en la base de datos sin incluir el campo estado_cliente
        $sql = 'INSERT INTO tb_clientes(nombre_cliente, apellido_cliente, correo_cliente, dui_cliente, telefono_cliente, fecha_de_nacimiento, fecha_de_registro, direccion_cliente, clave_cliente)
                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)';

        // Parámetros de la consulta SQL, usando propiedades de la clase
        $params = array($this->nombre_cliente, $this->apellido_cliente, $this->correo_cliente, $this->dui_cliente, $this->telefono_cliente, $this->fecha_de_nacimiento, $this->fecha_de_registro, $this->direccion_cliente, $this->clave_cliente);

        // Ejecuta la consulta de inserción y devuelve el resultado
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        // Consulta SQL para leer todos los clientes de la base de datos
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, correo_cliente, telefono_cliente, dui_cliente, estado_cliente
            FROM tb_clientes
            ORDER BY id_cliente';

        // Ejecuta la consulta y devuelve los resultados
        return Database::getRows($sql);
    }

    public function readOne()
    {
        // Consulta SQL para leer un solo cliente basado en su ID
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, correo_cliente, dui_cliente, telefono_cliente, fecha_de_nacimiento, fecha_de_registro, direccion_cliente, estado_cliente
            FROM tb_clientes
            WHERE id_cliente = ?';

        // Parámetros de la consulta SQL, usando el ID del cliente proporcionado por la clase
        $params = array($this->id_cliente);

        // Ejecuta la consulta y devuelve el resultado
        return Database::getRow($sql, $params);
    }

    public function readCliente()
    {
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, correo_cliente, dui_cliente, telefono_cliente, fecha_de_nacimiento, fecha_de_registro, direccion_cliente, estado_cliente
        FROM tb_clientes
        WHERE id_cliente = ?;'; // Consulta SQL para obtener los datos del administrador actual (logueado)
        $params = array($_SESSION['idCliente']); // Parámetros para la consulta SQL
        return Database::getRow($sql, $params); // Ejecución de la consulta SQL
    }

    public function updateRow()
    {
        // Consulta SQL para actualizar los datos de un cliente existente
        $sql = 'UPDATE tb_clientes
            SET nombre_cliente = ?, apellido_cliente = ?, dui_cliente = ?, correo_cliente = ?, telefono_cliente = ?, fecha_de_nacimiento = ?, estado_cliente = ?
            WHERE id_cliente = ?';

        // Parámetros de la consulta SQL, usando propiedades de la clase y el ID del cliente
        $params = array($this->nombre_cliente, $this->apellido_cliente, $this->dui_cliente, $this->correo_cliente, $this->telefono_cliente, $this->fecha_de_nacimiento, $this->estado_cliente, $this->id_cliente);

        // Ejecuta la consulta de actualización y devuelve el resultado
        return Database::executeRow($sql, $params);
    }

    public function updateStatus()
    {
        // Consulta SQL para actualizar el estado de un cliente (por ejemplo, activar/desactivar)
        $sql = 'UPDATE tb_clientes
            SET estado_cliente = ?
            WHERE id_cliente = ?';

        // Parámetros de la consulta SQL, usando propiedades de la clase y el ID del cliente
        $params = array($this->estado_cliente, $this->id_cliente);

        // Ejecuta la consulta de actualización y devuelve el resultado
        return Database::executeRow($sql, $params);
    }

    public function updatePassword()
    {
        // Consulta SQL para actualizar la contraseña de un cliente
        $sql = 'UPDATE tb_clientes
            SET clave_cliente = ?
            WHERE id_cliente = ?';

        // Parámetros de la consulta SQL, usando la nueva contraseña proporcionada por la clase y el ID del cliente
        $params = array($this->clave_cliente, $this->id_cliente);

        // Ejecuta la consulta de actualización y devuelve el resultado
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        // Consulta SQL para eliminar un cliente basado en su ID
        $sql = 'DELETE FROM tb_clientes
            WHERE id_cliente = ?';

        // Parámetros de la consulta SQL, usando el ID del cliente proporcionado por la clase
        $params = array($this->id_cliente);

        // Ejecuta la consulta de eliminación y devuelve el resultado
        return Database::executeRow($sql, $params);
    }

    public function checkDuplicate($value)
    {
        // Consulta SQL para verificar si ya existe un cliente con un DUI o correo electrónico específico
        $sql = 'SELECT id_cliente
            FROM tb_clientes
            WHERE (dui_cliente = ? OR correo_cliente = ?)
            AND id_cliente <> ?;';

        // Parámetros de la consulta SQL, usando el valor proporcionado y el ID del cliente de la clase
        $params = array($value, $value, $this->id_cliente);

        // Ejecuta la consulta y devuelve el resultado
        return Database::getRow($sql, $params);
    }

    public function readCantidadPedidosPorMes()
    {
        $sql = '
        SELECT
            c.id_cliente,
            COUNT(p.id_pedido_cliente) AS cantidad_pedidos,
            DATE_FORMAT(CURRENT_DATE(), "%M") AS mes_actual
        FROM
            tb_clientes c
        JOIN
            tb_pedidos_clientes p ON c.id_cliente = p.id_cliente
        WHERE
            MONTH(p.fecha_de_inicio) = MONTH(CURRENT_DATE())
            AND YEAR(p.fecha_de_inicio) = YEAR(CURRENT_DATE())
            AND c.id_cliente = ?
        GROUP BY
            c.id_cliente, c.nombre_cliente, c.apellido_cliente;
        ';
        $params = array($_SESSION['idCliente']);
        return Database::getRow($sql, $params);
    }
}
