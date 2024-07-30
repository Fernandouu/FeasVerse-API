

<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
*	Clase para manejar el comportamiento de los datos de la tabla CLIENTE.
*/
class PedidosHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    */
    protected $id_pedido_cliente = null;
    protected $id_cliente = null;
    protected $id_repartidor = null;
    protected $estado_pedido = null;
    protected $precio_total = null;
    protected $fecha_de_inicio = null;
    protected $fecha_de_entrega = null;
    protected $id_costo_de_envio_por_departamento = null;
    protected $id_detalles_pedido = null;
    protected $id_detalle_zapato = null;
    protected $cantidad_pedido = null;
    protected $precio_del_zapato = null;

    protected $id_comentario = null;
    protected $titulo_comentario = null;
    protected $descripcion_comentario = null;
    protected $calificacion_comentario = null;
    protected $estado_comentario = null;
    protected $fecha_del_comentario = null;

    //!PRIVADAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
    // Método para crear un nuevo comentario
    public function createDetallePedido()
    {
        $sql = 'CALL insert_detalle_pedido(?, ?, ?, ?);';
        $params = array(
            $_SESSION['idCliente'],
            $this->id_detalle_zapato,
            $this->cantidad_pedido,
            $this->precio_del_zapato
        );
        return Database::executeRow($sql, $params);
    }

    //!METODOS DE BUSQUEDA
    //SELECT PARA LEER TODOS LOS PEDIDOS REALIzADOS
    public function readAllOrders()
    {
        // Consulta SQL para obtener todos los pedidos realizados
        $sql = "SELECT tb_pedidos_clientes.id_pedido_cliente, id_trabajador,
        CONCAT(tb_trabajadores.nombre_trabajador,' ', tb_trabajadores.apellido_trabajador) AS nombre_repartidor,
        CONCAT(tb_clientes.nombre_cliente,' ', tb_clientes.apellido_cliente) AS nombre_cliente,
        correo_cliente,
        telefono_cliente,
        direccion_cliente,
        estado_pedido,
        fecha_de_inicio,
        fecha_de_entrega,
        precio_total,
        costo_de_envio,
        precio_total + costo_de_envio AS total_cobrar
        FROM tb_pedidos_clientes 
        INNER JOIN tb_trabajadores ON tb_trabajadores.id_trabajador = tb_pedidos_clientes.id_repartidor
        INNER JOIN tb_clientes ON tb_clientes.id_cliente = tb_pedidos_clientes.id_cliente
        INNER JOIN tb_costos_de_envio_por_departamento ON tb_pedidos_clientes.id_costo_de_envio_por_departamento = tb_costos_de_envio_por_departamento.id_costo_de_envio_por_departamento
        WHERE estado_pedido != 'Carrito';";
        // Ejecutar la consulta y devolver los resultados
        return Database::getRows($sql);
    }

    public function graficaPedidos(){
        $sql = "SELECT*FROM vista_estado_pedidos
        ";
        return Database::getRows($sql);
    }

    // Método para buscar pedidos según el estado y un término de búsqueda
    public function searchOrders($estado, $searchTerm)
    {
        // Inicializamos la consulta SQL
        $sql = "SELECT tb_pedidos_clientes.id_pedido_cliente, id_trabajador,
        CONCAT(tb_trabajadores.nombre_trabajador,' ', tb_trabajadores.apellido_trabajador) AS nombre_repartidor,
        CONCAT(tb_clientes.nombre_cliente,' ', tb_clientes.apellido_cliente) AS nombre_cliente,
        correo_cliente,
        telefono_cliente,
        direccion_cliente,
        estado_pedido,
        fecha_de_inicio,
        fecha_de_entrega,
        precio_total,
        costo_de_envio,
        precio_total + costo_de_envio AS total_cobrar
        FROM tb_pedidos_clientes 
        INNER JOIN tb_trabajadores ON tb_trabajadores.id_trabajador = tb_pedidos_clientes.id_repartidor
        INNER JOIN tb_clientes ON tb_clientes.id_cliente = tb_pedidos_clientes.id_cliente
        INNER JOIN tb_costos_de_envio_por_departamento ON tb_pedidos_clientes.id_costo_de_envio_por_departamento = tb_costos_de_envio_por_departamento.id_costo_de_envio_por_departamento";

        // Inicializamos los parámetros para la consulta
        $params = array();

        // Creamos condiciones basadas en los parámetros de entrada
        if (!empty($searchTerm)) {
            $sql .= " WHERE estado_pedido != 'Carrito' AND (tb_trabajadores.nombre_trabajador LIKE ?
            OR tb_trabajadores.apellido_trabajador LIKE ?
            OR tb_clientes.nombre_cliente LIKE ?
            OR tb_clientes.apellido_cliente LIKE ?
            OR tb_clientes.correo_cliente LIKE ?
            OR tb_clientes.telefono_cliente LIKE ?
            OR tb_clientes.direccion_cliente LIKE ?
            OR tb_costos_de_envio_por_departamento.costo_de_envio LIKE ?)";

            // Añadimos los términos de búsqueda a los parámetros
            $params = array_fill(0, 8, "%$searchTerm%");

            if (!empty($estado)) {
                $sql .= " AND tb_pedidos_clientes.estado_pedido = ?";
                $params[] = $estado;
            }
        } elseif (!empty($estado)) {
            $sql .= " WHERE tb_pedidos_clientes.estado_pedido = ? AND estado_pedido != 'Carrito';";
            $params[] = $estado;
        }

        // Ejecutamos la consulta y retornamos los resultados
        return Database::getRows($sql, $params);
    }

    //SELECT PARA VER LOS ZAPATOS DE LAS ORDENES
    public function readShoesOfOrders()
    {
        // Consulta SQL para obtener los detalles de los zapatos de un pedido específico
        $sql = "SELECT id_detalles_pedido, foto_detalle_zapato,
        nombre_zapato, nombre_color, num_talla, cantidad_pedido, tb_zapatos.precio_unitario_zapato,
        tb_zapatos.precio_unitario_zapato * cantidad_pedido AS precio_total
        FROM tb_detalles_pedidos
        INNER JOIN tb_detalle_zapatos 
        ON tb_detalle_zapatos.id_detalle_zapato = tb_detalles_pedidos.id_detalle_zapato
        INNER JOIN tb_zapatos
        ON tb_detalle_zapatos.id_zapato = tb_zapatos.id_zapato
        INNER JOIN tb_colores
        ON tb_colores.id_color = tb_detalle_zapatos.id_color
        INNER JOIN tb_tallas
        ON tb_tallas.id_talla = tb_detalle_zapatos.id_talla
        WHERE id_pedido_cliente = ?";
        $params = array($this->id_pedido_cliente);
        return Database::getRows($sql, $params);
    }

    //SELECT DE LOS TRABAJADORES PARA SABER LAS CLASES DE PEDIDOS QUE TIENEN O REALIZARON
    public function readAllOrdersWorkers()
    {
        // Consulta SQL para obtener el resumen de los pedidos realizados por cada trabajador
        $sql = "SELECT id_trabajador, nombre_trabajador, 
        apellido_trabajador, 
        dui_trabajador, 
        telefono_trabajador, 
        correo_trabajador, 
        SUM(CASE WHEN estado_pedido = ? THEN 1 ELSE 0 END) AS entregado,
        SUM(CASE WHEN estado_pedido = ? THEN 1 ELSE 0 END) AS en_proceso,
        SUM(CASE WHEN estado_pedido = ? THEN 1 ELSE 0 END) AS pendiente
        FROM tb_trabajadores 
        INNER JOIN tb_pedidos_clientes 
        ON tb_pedidos_clientes.id_repartidor = tb_trabajadores.id_trabajador
        WHERE estado_pedido != 'Carrito'
        GROUP BY 
        id_trabajador, nombre_trabajador, apellido_trabajador, dui_trabajador, telefono_trabajador, correo_trabajador";

        $params = array('Entregado', 'En camino', 'Pendiente');
        return Database::getRows($sql, $params);
    }

    // Método para buscar pedidos realizados por un trabajador específico
    public function searchOrdersWorkers($searchTerm)
    {
        // Consulta SQL para buscar pedidos realizados por trabajadores según un término de búsqueda
        $sql = "SELECT id_trabajador, nombre_trabajador, 
            apellido_trabajador, 
            dui_trabajador, 
            telefono_trabajador, 
            correo_trabajador, 
            SUM(CASE WHEN estado_pedido = ? THEN 1 ELSE 0 END) AS entregado,
            SUM(CASE WHEN estado_pedido = ? THEN 1 ELSE 0 END) AS en_proceso,
            SUM(CASE WHEN estado_pedido = ? THEN 1 ELSE 0 END) AS pendiente
            FROM tb_trabajadores 
            INNER JOIN tb_pedidos_clientes 
            ON tb_pedidos_clientes.id_repartidor = tb_trabajadores.id_trabajador
            WHERE (id_trabajador LIKE ? OR
                nombre_trabajador LIKE ? OR
                apellido_trabajador LIKE ? OR
                dui_trabajador LIKE ? OR
                telefono_trabajador LIKE ? OR
                correo_trabajador LIKE ?)
                AND estado_pedido != 'Carrito'
            GROUP BY 
            id_trabajador, nombre_trabajador, apellido_trabajador, dui_trabajador, telefono_trabajador, correo_trabajador";

        $params = array('Entregado', 'En camino', 'Pendiente', "%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%");
        return Database::getRows($sql, $params);
    }

    //SELECT PARA MOSTRAR LOS DIFERENTES workers 
    public function readOrdersOfWorkerCategories()
    {
        // Consulta SQL para obtener pedidos de un trabajador según su categoría
        $sql = "SELECT 
        tb_pedidos_clientes.id_pedido_cliente, 
        CONCAT(tb_clientes.nombre_cliente,' ', tb_clientes.apellido_cliente) AS nombre_cliente,
        correo_cliente,
        telefono_cliente,
        direccion_cliente,
        estado_pedido,
        fecha_de_inicio,
        fecha_de_entrega,
        precio_total,
        costo_de_envio,
        precio_total + costo_de_envio AS total_cobrar
        FROM tb_pedidos_clientes 
        INNER JOIN tb_trabajadores ON tb_trabajadores.id_trabajador = tb_pedidos_clientes.id_repartidor
        INNER JOIN tb_clientes ON tb_clientes.id_cliente = tb_pedidos_clientes.id_cliente
        INNER JOIN tb_costos_de_envio_por_departamento ON tb_pedidos_clientes.id_costo_de_envio_por_departamento = tb_costos_de_envio_por_departamento.id_costo_de_envio_por_departamento
        WHERE id_trabajador = ? AND estado_pedido = ? AND estado_pedido != 'Carrito';";
        $params = array($this->id_repartidor, $this->estado_pedido);
        return Database::getRows($sql, $params);
    }

    // Método para actualizar el estado de un pedido
    public function updateStatus()
    {
        // Iniciar la variable SQL y los parámetros
        $sql = 'UPDATE tb_pedidos_clientes SET estado_pedido = ?';
        $params = array($this->estado_pedido);

        // Verificar si el estado es "Entregado"
        if ($this->estado_pedido === 'Entregado') {
            // Agregar la fecha de entrega a la consulta SQL y a los parámetros
            $sql .= ', fecha_de_entrega = NOW()';
        }

        // Agregar la cláusula WHERE a la consulta SQL
        $sql .= ' WHERE id_pedido_cliente = ?';
        $params[] = $this->id_pedido_cliente;

        // Ejecutar la consulta SQL con los parámetros
        return Database::executeRow($sql, $params);
    }

    // Método para obtener la cantidad de ventas por mes
    public function ventasMes()
    {
        $sql = 'SELECT * FROM pedidos_entregados_por_mes;';
        return Database::getRows($sql);
    }

    //!PUBLICA

    //SELECT PARA LEER TODOS LOS PEDIDOS REALIzADOS
    public function readAllOrdersClients()
    {
        // Consulta SQL para obtener todos los pedidos realizados
        $sql = "SELECT id_pedido_cliente, id_trabajador,
        CONCAT(tb_trabajadores.nombre_trabajador,' ', tb_trabajadores.apellido_trabajador) AS nombre_repartidor,
        correo_trabajador,
        telefono_trabajador,
        estado_pedido,
        fecha_de_inicio,
        fecha_de_entrega,
        precio_total,
        costo_de_envio,
        precio_total + costo_de_envio AS total_cobrar
        FROM tb_pedidos_clientes 
        INNER JOIN tb_trabajadores ON tb_trabajadores.id_trabajador = tb_pedidos_clientes.id_repartidor
        INNER JOIN tb_clientes ON tb_clientes.id_cliente = tb_pedidos_clientes.id_cliente
        INNER JOIN tb_costos_de_envio_por_departamento ON tb_pedidos_clientes.id_costo_de_envio_por_departamento = tb_costos_de_envio_por_departamento.id_costo_de_envio_por_departamento
        WHERE estado_pedido != 'Carrito' AND tb_pedidos_clientes.id_cliente = ?;";

        $params = array($_SESSION['idCliente']);
        return Database::getRows($sql, $params);
    }

    //SELECT PARA LEER TODOS LOS PEDIDOS REALIzADOS
    public function comentario()
    {
        // Consulta SQL para obtener todos los pedidos realizados
        $sql = "SELECT dp.id_detalles_pedido, z.nombre_zapato
        FROM tb_detalle_zapatos dz
        INNER JOIN tb_zapatos z ON dz.id_zapato = z.id_zapato
        INNER JOIN tb_detalles_pedidos dp ON dz.id_detalle_zapato = dp.id_detalle_zapato
        INNER JOIN tb_pedidos_clientes p ON dp.id_pedido_cliente = p.id_pedido_cliente
        LEFT JOIN tb_comentarios c ON dp.id_detalles_pedido = c.id_detalles_pedido
        WHERE c.id_comentario IS NULL AND p.id_cliente = ?
        GROUP BY nombre_zapato;";

        $params = array($_SESSION['idCliente']);
        return Database::getRows($sql, $params);
    }

    // Método para crear un nuevo comentario
    public function createComentario()
    {
        $sql = 'INSERT INTO tb_comentarios (titulo_comentario, descripcion_comentario, calificacion_comentario, estado_comentario, fecha_del_comentario, id_detalles_pedido)
            VALUES (?, ?, ?, ?, ?, ?)';
        $params = array(
            $this->titulo_comentario,
            $this->descripcion_comentario,
            $this->calificacion_comentario,
            $this->estado_comentario,
            $this->fecha_del_comentario,
            $this->id_detalles_pedido
        );
        return Database::executeRow($sql, $params);
    }



    //SELECT PARA LEER TODOS LOS PEDIDOS REALIzADOS
    public function SearchOrdersClients($estado)
    {
        // Consulta SQL para obtener todos los pedidos realizados
        $sql = "SELECT id_pedido_cliente, id_trabajador,
        CONCAT(tb_trabajadores.nombre_trabajador,' ', tb_trabajadores.apellido_trabajador) AS nombre_repartidor,
        correo_trabajador,
        telefono_trabajador,
        estado_pedido,
        fecha_de_inicio,
        fecha_de_entrega,
        precio_total,
        costo_de_envio,
        precio_total + costo_de_envio AS total_cobrar
        FROM tb_pedidos_clientes 
        INNER JOIN tb_trabajadores ON tb_trabajadores.id_trabajador = tb_pedidos_clientes.id_repartidor
        INNER JOIN tb_clientes ON tb_clientes.id_cliente = tb_pedidos_clientes.id_cliente
        INNER JOIN tb_costos_de_envio_por_departamento ON tb_pedidos_clientes.id_costo_de_envio_por_departamento = tb_costos_de_envio_por_departamento.id_costo_de_envio_por_departamento
        WHERE estado_pedido != 'Carrito' AND tb_pedidos_clientes.id_cliente = ? AND tb_pedidos_clientes.estado_pedido = ?;";

        $params = array($_SESSION['idCliente'], $estado);
        return Database::getRows($sql, $params);
    }

    //SELECT PARA LEER TODOS LOS PEDIDOS REALIzADOS
    public function SearchOrdersClientsId($id)
    {
        // Consulta SQL para obtener todos los pedidos realizados
        $sql = "SELECT id_pedido_cliente, id_trabajador,
        CONCAT(tb_trabajadores.nombre_trabajador,' ', tb_trabajadores.apellido_trabajador) AS nombre_repartidor,
        correo_trabajador,
        telefono_trabajador,
        estado_pedido,
        fecha_de_inicio,
        fecha_de_entrega,
        precio_total,
        costo_de_envio,
        precio_total + costo_de_envio AS total_cobrar
        FROM tb_pedidos_clientes 
        INNER JOIN tb_trabajadores ON tb_trabajadores.id_trabajador = tb_pedidos_clientes.id_repartidor
        INNER JOIN tb_clientes ON tb_clientes.id_cliente = tb_pedidos_clientes.id_cliente
        INNER JOIN tb_costos_de_envio_por_departamento ON tb_pedidos_clientes.id_costo_de_envio_por_departamento = tb_costos_de_envio_por_departamento.id_costo_de_envio_por_departamento
        WHERE estado_pedido != 'Carrito' AND tb_pedidos_clientes.id_cliente = ? AND tb_pedidos_clientes.id_pedido_cliente = ?;";

        $params = array($_SESSION['idCliente'], $id);
        return Database::getRows($sql, $params);
    }


    // Método para eliminar un pedido
    public function deleteRowPedidos()
    {
        $sql = 'DELETE FROM tb_detalles_pedidos
                WHERE id_detalles_pedido = ?';
        $params = array($this->id_detalles_pedido);
        return Database::executeRow($sql, $params);
    }

    //CUD DE TBDETALLES
    // Método para crear un nuevo detalle de pedido
    public function createRowDetalle()
    {
        $sql = 'INSERT INTO tb_detalles_pedidos (id_pedido_cliente, id_detalle_zapato, cantidad_pedido, precio_del_zapato)
                VALUES (?, ?, ?, ?)';
        $params = array($this->id_pedido_cliente, $this->id_detalle_zapato, $this->cantidad_pedido, $this->precio_del_zapato);
        return Database::executeRow($sql, $params);
    }

    // Método para actualizar un detalle de pedido existente
    public function updateRowDetalle()
    {
        $sql = 'UPDATE tb_detalles_pedidos
                SET cantidad_pedido = ?
                WHERE id_detalles_pedido = ?';
        $params = array($this->cantidad_pedido, $this->id_detalles_pedido);
        return Database::executeRow($sql, $params);
    }

    // Método para eliminar un detalle de pedido
    public function deleteRowDetalle()
    {
        $sql = 'DELETE FROM tb_detalles_pedidos
                WHERE id_detalles_pedido = ?';
        $params = array($this->id_detalles_pedido);
        return Database::executeRow($sql, $params);
    }

    // Método para crear un nuevo pedido
    public function createRowPedidos()
    {
        $sql = 'INSERT INTO tb_pedidos_clientes (id_cliente, estado_pedido)
                VALUES(?, ?)';
        $params = array($_SESSION['idCliente'], $this->estado_pedido);
        return Database::executeRow($sql, $params);
    }

    // Método para actualizar un pedido existente
    public function updateRowPedidos()
    {
        $sql = 'UPDATE tb_pedidos_clientes
                SET id_repartidor = ?, estado_pedido = ?, precio_total = ?, fecha_de_inicio = ?, id_costo_de_envio_por_departamento = ?
                WHERE id_pedido_cliente = ?';
        $_SESSION['idPedidoCliente'] = $this->id_pedido_cliente;
        $params = array($this->id_repartidor, $this->estado_pedido, $this->precio_total, $this->fecha_de_inicio, $this->id_costo_de_envio_por_departamento, $this->id_pedido_cliente);
        return Database::executeRow($sql, $params);
    }

    //SELECT PARA LEER TODOS LOS PEDIDOS REALIzADOS
    public function readAllOrdersFactura()
    {
        // Consulta SQL para obtener todos los pedidos realizados
        $sql = "SELECT tb_pedidos_clientes.id_pedido_cliente, id_trabajador,
        CONCAT(tb_trabajadores.nombre_trabajador,' ', tb_trabajadores.apellido_trabajador) AS nombre_repartidor,
        CONCAT(tb_clientes.nombre_cliente,' ', tb_clientes.apellido_cliente) AS nombre_cliente,
        correo_cliente,
        telefono_cliente,
        direccion_cliente,
        estado_pedido,
        fecha_de_inicio,
        fecha_de_entrega,
        precio_total,
        costo_de_envio,
        precio_total + costo_de_envio AS total_cobrar
        FROM tb_pedidos_clientes 
        INNER JOIN tb_trabajadores ON tb_trabajadores.id_trabajador = tb_pedidos_clientes.id_repartidor
        INNER JOIN tb_clientes ON tb_clientes.id_cliente = tb_pedidos_clientes.id_cliente
        INNER JOIN tb_costos_de_envio_por_departamento ON tb_pedidos_clientes.id_costo_de_envio_por_departamento = tb_costos_de_envio_por_departamento.id_costo_de_envio_por_departamento
        WHERE estado_pedido != 'Carrito' AND id_pedido_cliente = ?;";
        
        $params = array($_SESSION['idPedidoCliente']);
        return Database::getRows($sql, $params);
    }

    //SELECT PARA VER LOS ZAPATOS DE LAS ORDENES
    public function readShoesOfCarritos()
    {
        // Consulta SQL para obtener los detalles de los zapatos de un pedido específico
        $sql = "SELECT tb_pedidos_clientes.id_pedido_cliente, tb_detalle_zapatos.id_detalle_zapato, id_detalles_pedido, foto_detalle_zapato,
        nombre_zapato, nombre_color, num_talla, cantidad_pedido, tb_zapatos.precio_unitario_zapato,
        tb_zapatos.precio_unitario_zapato * cantidad_pedido AS precio_total
        FROM tb_detalles_pedidos
        INNER JOIN tb_detalle_zapatos 
        ON tb_detalle_zapatos.id_detalle_zapato = tb_detalles_pedidos.id_detalle_zapato
        INNER JOIN tb_zapatos
        ON tb_detalle_zapatos.id_zapato = tb_zapatos.id_zapato
        INNER JOIN tb_colores
        ON tb_colores.id_color = tb_detalle_zapatos.id_color
        INNER JOIN tb_tallas
        ON tb_tallas.id_talla = tb_detalle_zapatos.id_talla
        INNER JOIN tb_pedidos_clientes
        ON tb_pedidos_clientes.id_pedido_cliente = tb_detalles_pedidos.id_pedido_cliente
        WHERE id_cliente = ? AND estado_pedido = 'Carrito';";
        $params = array($_SESSION['idCliente']);
        return Database::getRows($sql, $params);
    }

    public function readRepartidores()
    {
        $sql = "SELECT id_trabajador FROM tb_trabajadores WHERE id_nivel = 3
                ORDER BY RAND()
                LIMIT 1;";
        return Database::getRow($sql);
    }

    //SELECT PARA VER PRECIOS
    public function readPrecio()
    {
        // Consulta SQL para obtener un detalle de zapato aleatorio de un pedido específico
        $sql = "SELECT id_costo_de_envio_por_departamento, costo_de_envio
            FROM tb_costos_de_envio_por_departamento
            ORDER BY RAND()
            LIMIT 1;";
        return Database::getRow($sql); // Asumiendo que getRow devuelve una sola fila
    }


    //SELECT PARA VER LOS ZAPATOS DE LAS ORDENES
    public function verCarrito()
    {
        // Consulta SQL para obtener los detalles de los zapatos de un pedido específico
        $sql = "SELECT*FROM tb_pedidos_clientes
        WHERE id_cliente = ? AND estado_pedido = 'Carrito';";
        $params = array($_SESSION['idCliente']);
        return Database::getRows($sql, $params);
    }
}