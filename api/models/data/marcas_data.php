<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/marcas_handler.php');

/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla USUARIO.
 */
class MarcasData extends MarcasHandler
{
    // Atributo genérico para manejo de errores.
    private $data_error = null;
    private $filename = null;

    /*
     *  Métodos para validar y asignar valores de los atributos.
     */

    // Método para establecer el ID de la marca
    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_marca = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de la marca es incorrecto';
            return false;
        }
    }

    // Método para establecer el nombre de la marca
    public function setNombreMarca($value, $min = 2, $max = 20)
    {
        if (!Validator::validateAlphabetic($value)) {
            $this->data_error = 'El nombre debe ser un valor alfabético';
            return false;
        } elseif (!Validator::validateLength($value, $min, $max)) {
            $this->data_error = 'El nombre debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        } elseif($this->checkDuplicate($value)) {
            $this->data_error = 'El nombre ingresado ya existe';
            return false;
        } else {
            $this->nombre_marca = $value;
            return true;
        }
    }

    // Método para establecer la descripción de la marca
    public function setDescripcionMarca($value, $min = 20, $max = 100)
    {
        if (Validator::validateLength($value, $min, $max)) {
            $this->descripcion_marca = $value;
            return true;
        } else {
            $this->data_error = 'La descripción debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    // Método para establecer la foto de la marca
    public function setFotoMarca($file, $filename = null)
    {
        if (Validator::validateImageFile($file, 150)) {
            $this->foto_marca = Validator::getFileName();
            return true;
        } elseif (Validator::getFileError()) {
            $this->data_error = Validator::getFileError();
            return false;
        } elseif ($filename) {
            $this->foto_marca = $filename;
            return true;
        } else {
            $this->foto_marca = 'default.png';
            return true;
        }
    }

    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }

    // Método para establecer el nombre de archivo
    public function setFilename()
    {
        if ($data = $this->readFilename()) {
            $this->filename = $data['foto_marca'];
            return true;
        } else {
            $this->data_error = 'Marca inexistente';
            return false;
        }
    }

    // Método para obtener el nombre de archivo
    public function getFilename()
    {
        return $this->filename;
    }
}
