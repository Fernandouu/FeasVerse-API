<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */

class ZapatosHandler
{

    protected $id_zapato = null;
    protected $id_trabajador = null;
    protected $id_marca = null;
    protected $nombre_zapato = null;
    protected $genero_zapato = null;
    protected $descripcion_zapato = null;
    protected $estado_zapato = null;
    protected $precio_unitario_zapato = null;
    protected $foto_detalle_zapato = null;
    protected $id_color = null;
    protected $nombre_color = null;
    protected $id_talla = null;
    protected $cantidad_zapato = null;
    protected $num_talla = null;
    protected $id_detalle_zapato = null;

    const RUTA_IMAGEN = '../../helpers/images/zapatos/';

    public function updateRowZapato()
    {
        $sql = 'UPDATE tb_zapatos
        SET
            id_marca = ?,
            nombre_zapato = ?,
            genero_zapato = ?,
            descripcion_zapato = ?,
            precio_unitario_zapato = ?
        WHERE id_zapato = ?;';

        $params = array(
            $this->id_marca,
            $this->nombre_zapato,
            $this->genero_zapato,
            $this->descripcion_zapato,
            $this->precio_unitario_zapato,
            $this->id_zapato
        );
        return Database::executeRow($sql, $params);
    }
    public function deleteDetalle()
    {
        $sql = 'CALL CambiarEstadoZapato(?);';
        $params = array(
            $this->id_detalle_zapato
        );

        return Database::executeRow($sql, $params);
    }

    public function readTopZapatos()
    {
        $sql = 'SELECT * FROM vw_top_5_zapatos_por_calificacion;';
        return Database::getRows($sql);
    }



    public function updateDetalle()
    {
        $sql = 'UPDATE tb_detalle_zapatos SET 
            cantidad_zapato = ?
           WHERE id_detalle_zapato = ?';

        $params = array(
            $this->cantidad_zapato,
            $this->id_detalle_zapato
        );

        return Database::executeRow($sql, $params);
    }


    public function createRowDetalle()
    {
        $sql = 'INSERT INTO tb_detalle_zapatos (id_zapato, id_talla, cantidad_zapato, id_color, foto_detalle_zapato)
           VALUES (?, ?, ?, ?, "default.png");';

        $params = array(
            $this->id_zapato,
            $this->id_talla,
            $this->cantidad_zapato,
            $this->id_color
        );
        return Database::executeRow($sql, $params);
    }

    public function readMasVendido()
    {
        $sql = '(
            SELECT z.id_zapato, z.nombre_zapato, dz.foto_detalle_zapato, SUM(dp.cantidad_pedido) AS total_vendido
            FROM tb_zapatos z
            INNER JOIN tb_detalle_zapatos dz ON z.id_zapato = dz.id_zapato
            JOIN tb_detalles_pedidos dp ON dz.id_detalle_zapato = dp.id_detalle_zapato
            GROUP BY z.id_zapato, z.nombre_zapato
            ORDER BY total_vendido DESC
            LIMIT 1
        )
        UNION ALL
        (
            SELECT z.id_zapato, z.nombre_zapato, dz.foto_detalle_zapato, 0 AS total_vendido
            FROM tb_zapatos z
            INNER JOIN tb_detalle_zapatos dz
            WHERE NOT EXISTS (
                SELECT 1
                FROM tb_detalle_zapatos dz
                JOIN tb_detalles_pedidos dp ON dz.id_detalle_zapato = dp.id_detalle_zapato
                WHERE dz.id_zapato = z.id_zapato
            )
            ORDER BY z.id_zapato
            LIMIT 1
        )
        LIMIT 1;';
        return Database::getRow($sql);
    }

    public function readAll()
    {
        $sql = 'SELECT zapatos.id_zapato, zapatos.nombre_zapato,  detalle_zapatos.foto_detalle_zapato  FROM  tb_zapatos AS zapatos
        INNER JOIN tb_detalle_zapatos AS detalle_zapatos ON zapatos.id_zapato = detalle_zapatos.id_zapato GROUP BY id_zapato;';
        return Database::getRows($sql);
    }

    public function readResumeAllZapatos()
    {
        $sql = 'SELECT z.id_zapato, z.nombre_zapato, z.genero_zapato, z.precio_unitario_zapato, dz.foto_detalle_zapato, COUNT(DISTINCT dz.id_color) AS colores, ROUND(AVG(c.calificacion_comentario), 2) AS estrellas 
        FROM tb_zapatos z
        INNER JOIN tb_detalle_zapatos dz ON dz.id_zapato = z.id_zapato
        LEFT JOIN tb_detalles_pedidos dp ON dz.id_detalle_zapato = dp.id_detalle_zapato
        LEFT JOIN tb_comentarios c ON c.id_detalles_pedido = dp.id_detalles_pedido
        GROUP BY z.id_zapato;';
        return Database::getRows($sql);
    }

    public function readResumeAllZapatosMarca()
    {
        $sql = 'SELECT z.id_zapato, z.nombre_zapato, z.genero_zapato, z.precio_unitario_zapato, dz.foto_detalle_zapato, COUNT(DISTINCT dz.id_color) AS colores, ROUND(AVG(c.calificacion_comentario), 2) AS estrellas 
        FROM tb_zapatos z
        INNER JOIN tb_detalle_zapatos dz ON dz.id_zapato = z.id_zapato
        LEFT JOIN tb_detalles_pedidos dp ON dz.id_detalle_zapato = dp.id_detalle_zapato
        LEFT JOIN tb_comentarios c ON c.id_detalles_pedido = dp.id_detalles_pedido
        WHERE z.id_marca = ? 
        GROUP BY z.id_zapato;';
        $params = array(
            $this->id_marca
        );
        return Database::getRows($sql, $params);
    }

    public function validationCantidad()
    {
        $sql = 'SELECT cantidad_zapato FROM tb_detalle_zapatos WHERE id_detalle_zapato = ?;';
        $params = array(
            $this->id_detalle_zapato
        );
        return Database::getRow($sql, $params);
    }

    public function searchDetalle()
    {
        $sql = 'SELECT id_detalle_zapato FROM tb_detalle_zapatos WHERE id_talla = ? AND id_color = ? AND id_zapato = ? LIMIT 1';
        $params = array(
            $this->id_talla,
            $this->id_color,
            $this->id_zapato
        );
        return Database::getRow($sql, $params);
    }

    public function readOneResegnas()
    {
        $sql = 'SELECT co.id_comentario, cl.nombre_cliente, cl.apellido_cliente, co.fecha_del_comentario, co.titulo_comentario, co.descripcion_comentario, co.calificacion_comentario FROM tb_clientes cl
        INNER JOIN tb_pedidos_clientes pc ON pc.id_cliente = cl.id_cliente
        INNER JOIN tb_detalles_pedidos dp ON dp.id_pedido_cliente = pc.id_pedido_cliente
        INNER JOIN tb_detalle_zapatos dz ON dz.id_detalle_zapato = dp.id_detalle_zapato
        INNER JOIN tb_comentarios co ON co.id_detalles_pedido = dp.id_detalles_pedido
        WHERE dz.id_zapato = ? AND co.estado_comentario != "Desactivo";';
        $params = array($this->id_zapato);
        return Database::getRows($sql, $params);
    }

    public function readOneTallas()
    {
        $sql = 'SELECT DISTINCT t.id_talla, t.num_talla FROM tb_tallas t
        INNER JOIN tb_detalle_zapatos dz ON dz.id_talla = t.id_talla
        WHERE dz.id_zapato = ? AND dz.cantidad_zapato > 0;';
        $params = array($this->id_zapato);
        return Database::getRows($sql, $params);
    }

    public function readTallasDisponiblesForColor()
    {
        $sql = 'SELECT DISTINCT t.id_talla, t.num_talla, dz.cantidad_zapato FROM tb_tallas t
        INNER JOIN tb_detalle_zapatos dz ON dz.id_talla = t.id_talla
        WHERE dz.id_zapato = ? AND dz.id_color = ? AND dz.cantidad_zapato > 0;';
        $params = array($this->id_zapato, $this->id_color);
        return Database::getRows($sql, $params);
    }

    public function readOneColoresZapato()
    {
        $sql = 'SELECT DISTINCT c.id_color, c.nombre_color FROM tb_colores c
        INNER JOIN tb_detalle_zapatos dz ON dz.id_color = c.id_color
        WHERE dz.id_zapato = ? AND dz.cantidad_zapato > 0;';
        $params = array($this->id_zapato);
        return Database::getRows($sql, $params);
    }

    public function readColoresDisponiblesForTalla()
    {
        $sql = 'SELECT DISTINCT c.id_color, c.nombre_color, id_zapato, cantidad_zapato
                FROM tb_detalle_zapatos dz
                INNER JOIN tb_colores c ON dz.id_color = c.id_color
                WHERE dz.id_zapato = ? AND dz.id_talla = ?';
        $params = array($this->id_zapato, $this->id_talla);
        return Database::getRows($sql, $params);
    }

    public function readOneDetail()
    {
        $sql = 'SELECT z.id_zapato, dz.foto_detalle_zapato, m.nombre_marca, z.nombre_zapato, z.genero_zapato, z.precio_unitario_zapato, ROUND(AVG(c.calificacion_comentario), 2) AS estrellas, z.descripcion_zapato
        FROM tb_zapatos z
        INNER JOIN tb_detalle_zapatos dz ON dz.id_zapato = z.id_zapato
        LEFT JOIN tb_detalles_pedidos dp ON dz.id_detalle_zapato = dp.id_detalle_zapato
        LEFT JOIN tb_comentarios c ON c.id_detalles_pedido = dp.id_detalles_pedido
        INNER JOIN tb_marcas m ON m.id_marca = z.id_marca WHERE z.id_zapato = ?;';
        $params = array($this->id_zapato);
        return Database::getRow($sql, $params);
    }

    public function readResumeReciente()
    {
        $sql = 'SELECT z.id_zapato, z.nombre_zapato, z.genero_zapato, z.precio_unitario_zapato, dz.foto_detalle_zapato, COUNT(DISTINCT dz.id_color) AS colores, ROUND(AVG(c.calificacion_comentario), 2) AS estrellas 
        FROM tb_zapatos z
        INNER JOIN tb_detalle_zapatos dz ON dz.id_zapato = z.id_zapato
        LEFT JOIN tb_detalles_pedidos dp ON dz.id_detalle_zapato = dp.id_detalle_zapato
        LEFT JOIN tb_comentarios c ON c.id_detalles_pedido = dp.id_detalles_pedido
        GROUP BY z.id_zapato
        ORDER BY z.id_zapato DESC LIMIT 10;';
        return Database::getRows($sql);
    }

    public function readResumeEspecial()
    {
        $sql = 'SELECT z.id_zapato, z.nombre_zapato, z.genero_zapato, z.precio_unitario_zapato, dz.foto_detalle_zapato, COUNT(DISTINCT dz.id_color) AS colores, ROUND(AVG(c.calificacion_comentario), 2) AS estrellas 
        FROM tb_zapatos z
        INNER JOIN tb_detalle_zapatos dz ON dz.id_zapato = z.id_zapato
        LEFT JOIN tb_detalles_pedidos dp ON dz.id_detalle_zapato = dp.id_detalle_zapato
        LEFT JOIN tb_comentarios c ON c.id_detalles_pedido = dp.id_detalles_pedido
        GROUP BY z.id_zapato 
        ORDER BY estrellas DESC LIMIT 10;';
        return Database::getRows($sql);
    }

    public function readAllZapatoMarca()
    {
        $sql = 'SELECT z.id_zapato, z.nombre_zapato, z.genero_zapato, z.precio_unitario_zapato, dz.foto_detalle_zapato, COUNT(DISTINCT dz.id_color) AS colores, 
        ROUND(AVG(c.calificacion_comentario), 2) AS estrellas, z.estado_zapato
        FROM tb_zapatos z
        INNER JOIN tb_detalle_zapatos dz ON dz.id_zapato = z.id_zapato
        INNER JOIN tb_colores ON tb_colores.id_color = dz.id_color
        LEFT JOIN tb_detalles_pedidos dp ON dz.id_detalle_zapato = dp.id_detalle_zapato
        LEFT JOIN tb_comentarios c ON c.id_detalles_pedido = dp.id_detalles_pedido
        WHERE id_marca = ?
        GROUP BY z.id_zapato
        ORDER BY z.id_zapato;';
        $params = array($this->id_marca);
        return Database::getRows($sql, $params);
    }

    public function searchValueZapatoMarca()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT z.id_zapato, z.nombre_zapato, z.genero_zapato, z.precio_unitario_zapato, dz.foto_detalle_zapato, COUNT(DISTINCT dz.id_color) AS colores, 
        ROUND(AVG(c.calificacion_comentario), 2) AS estrellas, z.estado_zapato
        FROM tb_zapatos z
        INNER JOIN tb_detalle_zapatos dz ON dz.id_zapato = z.id_zapato
        INNER JOIN tb_colores ON tb_colores.id_color = dz.id_color
        LEFT JOIN tb_detalles_pedidos dp ON dz.id_detalle_zapato = dp.id_detalle_zapato
        LEFT JOIN tb_comentarios c ON c.id_detalles_pedido = dp.id_detalles_pedido
        WHERE z.nombre_zapato LIKE ? AND z.id_marca = ?
        GROUP BY z.id_zapato
        ORDER BY z.id_zapato;';
        $params = array($value, $this->id_marca);
        return Database::getRows($sql, $params);
    }

    public function searchValue()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT z.id_zapato, z.nombre_zapato, z.genero_zapato, z.precio_unitario_zapato, dz.foto_detalle_zapato, COUNT(DISTINCT dz.id_color) AS colores, 
        ROUND(AVG(c.calificacion_comentario), 2) AS estrellas, z.estado_zapato
        FROM tb_zapatos z
        INNER JOIN tb_detalle_zapatos dz ON dz.id_zapato = z.id_zapato
        INNER JOIN tb_colores ON tb_colores.id_color = dz.id_color
        LEFT JOIN tb_detalles_pedidos dp ON dz.id_detalle_zapato = dp.id_detalle_zapato
        LEFT JOIN tb_comentarios c ON c.id_detalles_pedido = dp.id_detalles_pedido
        WHERE z.nombre_zapato LIKE ?
        GROUP BY z.id_zapato
        ORDER BY z.id_zapato;';
        $params = array($value);
        return Database::getRows($sql, $params);
    }

    public function readAllColores()
    {
        $sql = 'SELECT nombre_color, id_color FROM tb_colores;';
        return Database::getRows($sql);
    }

    public function readColoresPublic()
    {
        $sql = 'SELECT id_color, nombre_color FROM tb_colores;';
        return Database::getRows($sql);
    }

    public function readOneColores()
    {
        $sql = 'SELECT id_color, nombre_color FROM tb_colores WHERE id_color = ?;';
        $params = array($this->id_color);
        return Database::getRows($sql, $params);
    }

    public function readOneTalla()
    {
        $sql = 'SELECT id_talla, num_talla FROM tb_tallas WHERE id_talla = ?;';
        $params = array($this->id_talla);
        return Database::getRows($sql, $params);
    }


    public function ActColores()
    {
        $sql = 'UPDATE tb_colores 
        SET nombre_color = ?
        WHERE id_color = ?;';
        $params = array($this->nombre_color, $this->id_color); // Parámetros para la consulta SQL
        return Database::executeRow($sql, $params); // Ejecución de la consulta SQL
    }

    public function ActTallas()
    {
        $sql = 'UPDATE tb_tallas 
        SET num_talla = ?
        WHERE id_talla = ?;';
        $params = array($this->num_talla, $this->id_talla); // Parámetros para la consulta SQL
        return Database::executeRow($sql, $params); // Ejecución de la consulta SQL
    }

    public function addColores()
    {
        $sql = 'INSERT INTO tb_colores(nombre_color) VALUES (?)';
        $params = array(
            $this->nombre_color
        );
        return Database::executeRow($sql, $params);
    }
    public function addTallas()
    {
        $sql = 'INSERT INTO tb_tallas(num_talla) VALUES (?)';
        $params = array(
            $this->num_talla
        );
        return Database::executeRow($sql, $params);
    }

    public function readFilename()
    {
        $sql = 'SELECT foto_detalle_zapato
                FROM tb_detalle_zapatos
                WHERE id_detalle_zapato = ?;';
        $params = array($this->id_zapato);
        return Database::getRow($sql, $params);
    }

    public function checkDuplicate($value)
    {
        $sql = 'SELECT id_color from tb_colores WHERE nombre_color = ?';
        $params = array($value);
        return Database::getRow($sql, $params);
    }

    public function checkDuplicate2($value)
    {
        $sql = 'SELECT id_talla from tb_tallas WHERE num_talla = ?';
        $params = array($value);
        return Database::getRow($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO tb_zapatos(id_trabajador, id_marca , nombre_zapato, genero_zapato, descripcion_zapato, precio_unitario_zapato, estado_zapato) VALUES (?,?,?,?,?,?,"Activo");';
        $params = array(
            $_SESSION['idTrabajador'],
            $this->id_marca,
            $this->nombre_zapato,
            $this->genero_zapato,
            $this->descripcion_zapato,
            $this->precio_unitario_zapato
        );
        return Database::executeRow($sql, $params);
    }

    public function createRowPT2()
    {
        $sql = 'CALL InsertarDetalleZapato(?, ?, ?, ?);';
        $params = array(
            $this->id_talla,
            $this->cantidad_zapato,
            $this->id_color,
            $this->foto_detalle_zapato
        );
        return Database::executeRow($sql, $params);
    }
    // Método para leer todos los niveles
    public function readMarcas()
    {
        $sql = 'SELECT id_marca, nombre_marca from tb_marcas; '; // Consulta SQL para obtener todos los niveles
        return Database::getRows($sql); // Ejecución de la consulta SQL
    }

    public function readTallas()
    {
        $sql = 'SELECT*FROM tb_tallas;'; // Consulta SQL para obtener todos los niveles
        return Database::getRows($sql); // Ejecución de la consulta SQL
    }

    public function searchTallas($value)
    {
        // Obtiene el valor de búsqueda del validador y lo formatea para buscar coincidencias parciales en la base de datos


        $sql = 'SELECT id_talla, num_talla FROM tb_tallas where num_talla LIKE ?;'; // Consulta SQL para obtener todos los niveles
        // Parámetros de la consulta SQL
        $params = array($value);

        // Ejecuta la consulta y devuelve los resultados
        return Database::getRows($sql, $params);
    }

    public function DeleteTallas()
    {
        $sql = 'DELETE FROM tb_tallas 
        WHERE id_talla = ?;';
        $params = array($this->id_talla); // Parámetros para la consulta SQL
        return Database::executeRow($sql, $params); // Ejecución de la consulta SQL
    }

    public function readColores()
    {
        $sql = 'SELECT id_color, nombre_color from tb_colores; '; // Consulta SQL para obtener todos los niveles
        return Database::getRows($sql); // Ejecución de la consulta SQL
    }

    public function searchRows()
    {
        // Obtiene el valor de búsqueda del validador y lo formatea para buscar coincidencias parciales en la base de datos
        $value = '%' . Validator::getSearchValue() . '%';

        // Consulta SQL para buscar zapatos cuyo nombre o género coincidan parcialmente con el valor de búsqueda
        $sql = 'SELECT 
                    z.id_zapato, 
                    z.id_trabajador, 
                    z.id_marca, 
                    z.nombre_zapato, 
                    z.genero_zapato, 
                    z.descripcion_zapato, 
                    z.precio_unitario_zapato, 
                    z.estado_zapato,
                    d.foto_detalle_zapato
                FROM 
                    tb_zapatos z
                INNER  JOIN 
                    tb_detalle_zapatos d
                ON 
                    z.id_zapato = d.id_detalle_zapato
                WHERE 
                    z.nombre_zapato LIKE ? OR 
                    z.genero_zapato LIKE ?
                ORDER BY 
                    z.id_zapato;';

        // Parámetros de la consulta SQL
        $params = array($value, $value);

        // Ejecuta la consulta y devuelve los resultados
        return Database::getRows($sql, $params);
    }

    public function readOneZapato()
    {
        $sql = 'SELECT id_zapato, nombre_zapato, genero_Zapato, descripcion_zapato, id_marca, precio_unitario_zapato FROM tb_Zapatos 
        WHERE id_Zapato = ?;'; // Consulta SQL para obtener los datos de un zapato por ID
        $params = array($this->id_zapato); // Parámetros para la consulta SQL
        return Database::getRow($sql, $params); // Ejecución de la consulta SQL
    }

    public function readDetallesZapatos()
    {
        $sql = "SELECT id_detalle_zapato, id_talla, cantidad_zapato, nombre_color, foto_detalle_zapato AS 'foto' FROM tb_detalle_zapatos INNER JOIN tb_colores ON tb_colores.id_color = tb_detalle_zapatos.id_color 
        WHERE id_zapato = ?;"; // Consulta SQL para obtener los datos de un zapato por ID
        $params = array($this->id_zapato); // Parámetros para la consulta SQL
        return Database::getRows($sql, $params); // Ejecución de la consulta SQL
    }

    public function readFtoDetalle()
    {
        $sql = "SELECT  foto_detalle_zapato  FROM tb_detalle_zapatos
        WHERE id_zapato = ?  LIMIT 1;"; // Consulta SQL para obtener los datos de un zapato por ID
        $params = array($this->id_zapato); // Parámetros para la consulta SQL
        return Database::getRow($sql, $params); // Ejecución de la consulta SQL
    }

    public function searchZapatoMarca($tallas = [])
    {
        $sql = 'SELECT z.id_zapato, z.nombre_zapato, z.genero_zapato, z.precio_unitario_zapato, dz.foto_detalle_zapato, COUNT(DISTINCT dz.id_color) AS colores, ROUND(AVG(c.calificacion_comentario), 2) AS estrellas 
            FROM tb_zapatos z
            INNER JOIN tb_detalle_zapatos dz ON dz.id_zapato = z.id_zapato
            INNER JOIN tb_colores ON tb_colores.id_color = dz.id_color
            INNER JOIN tb_tallas t ON t.id_talla = dz.id_talla
            LEFT JOIN tb_detalles_pedidos dp ON dz.id_detalle_zapato = dp.id_detalle_zapato
            LEFT JOIN tb_comentarios c ON c.id_detalles_pedido = dp.id_detalles_pedido
            WHERE z.id_marca = ?';

        $params = array($this->id_marca);

        if ($this->nombre_zapato) {
            $sql .= ' AND z.nombre_zapato LIKE ?';
            $params[] = "%{$this->nombre_zapato}%";
        }
        if ($this->id_color !== null) {
            $sql .= ' AND dz.id_color = ?';
            $params[] = $this->id_color;
        }
        if (!empty($tallas)) {
            $talla_placeholders = implode(',', array_fill(0, count($tallas), '?'));
            $sql .= " AND t.num_talla IN ($talla_placeholders)";
            $params = array_merge($params, $tallas);
        }

        $sql .= ' GROUP BY z.id_zapato
                ORDER BY z.id_zapato;';

        return Database::getRows($sql, $params);
    }
}
