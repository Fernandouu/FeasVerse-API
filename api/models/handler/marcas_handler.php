<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class MarcasHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id_marca = null;
    protected $nombre_marca = null;
    protected $foto_marca = null;
    protected $descripcion_marca = null;

    // Ruta donde se guardarán las imágenes de las marcas
    const RUTA_IMAGEN = '../../helpers/images/marcas/';

    // Método para leer todas las marcas
    public function readPorcentajeZapatosMarca()
    {
        $sql = 'SELECT * FROM vw_porcentaje_zapatos_por_marca;';
        return Database::getRows($sql);
    }


    // Método para leer todas las marcas
    public function readAll()
    {
        $sql = 'SELECT id_marca, foto_marca FROM tb_marcas';
        return Database::getRows($sql);
    }

    // Método para crear una nueva marca
    public function createRow()
    {
        $sql = 'INSERT INTO tb_marcas(nombre_marca, foto_marca, descripcion_marca) VALUES (?,?,?)';
        $params = array(
            $this->nombre_marca,
            $this->foto_marca,
            $this->descripcion_marca
        );
        return Database::executeRow($sql, $params);
    }

    // Método para verificar si ya existe una marca con el mismo nombre
    public function checkDuplicate($value)
    {
        $sql = 'SELECT id_marca WHERE nombre_marca = ?';
        $params = array($value);
        return Database::getRow($sql, $params);
    }

    // Método para leer una marca específica
    public function readOne()
    {
        $sql = 'SELECT id_marca, nombre_marca, foto_marca, descripcion_marca FROM tb_marcas WHERE id_marca = ?';
        $params = array($this->id_marca);
        return Database::getRow($sql, $params);
    }

    // Método para actualizar una marca
    public function updateRow()
    {
        $sql = 'UPDATE tb_marcas SET
        nombre_marca = ?,
        foto_marca = ?,
        descripcion_marca = ? WHERE id_marca = ?;';
        $params = array(
            $this->nombre_marca,
            $this->foto_marca,
            $this->descripcion_marca,
            $this->id_marca,
        );
        return Database::executeRow($sql, $params);
    }

    // Método para obtener el nombre del archivo de la imagen de una marca
    public function readFilename()
    {
        $sql = 'SELECT foto_marca
                FROM tb_marcas
                WHERE id_marca = ?;';
        $params = array($this->id_marca);
        return Database::getRow($sql, $params);
    }

    // Método para buscar marcas por nombre o descripción
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_marca, nombre_marca, foto_marca, descripcion_marca
        FROM tb_marcas
        WHERE nombre_marca LIKE ? OR descripcion_marca LIKE ?;';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }
}
