<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/zapatos_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla USUARIO.
 */
class ZapatosData extends ZapatosHandler{

    private $data_error = null;
    private $filename = null;

    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_zapato = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del zapato es incorrecto';
            return false;
        }
    }

    public function setIdDetalleZapato($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_detalle_zapato = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del detalle zapato es incorrecto';
            return false;
        }
    }

    public function setIdTalla($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_talla = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de la talla es incorrecto';
            return false;
        }
    }

    public function setGenero($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->genero_zapato = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del genero es incorrecto';
            return false;
        }
    }

    public function setCantidad($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->cantidad_zapato = $value;
            return true;
        } else {
            $this->data_error = 'La cantidad es incorrecta incorrecto';
            return false;
        }
    }

    public function setTallas($value)
    {
        $numero = (int)$value;
        if (!Validator::validateNaturalNumber($numero)) {
            $this->data_error = 'La talla debe de ser numero';
            return false;
        } elseif ($this->checkDuplicate2($numero)) {
            $this->data_error = 'La talla ingresada ya existe';
            return false;
        } else {    
            $this->num_talla = $numero;
            return true;
        }
    }
    
    public function setIdColor($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_color = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del color es incorrecto';
            return false;
        }
    }

    public function setIdMarca($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_marca = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de la marca es incorrecto';
            return false;
        }
    }

    public function setPrecio($value)
    {
        if (Validator::validateMoney($value)) {
            $this->precio_unitario_zapato = $value;
            return true;
        } else {
            $this->data_error = 'El precio total debe ser un número válido';
            return false;
        }
    }


    public function setFotoZapato($file, $filename = null)
    {
        if (Validator::validateImageFile($file, 150)) {
            $this->foto_detalle_zapato = Validator::getFileName();
            return true;
        } elseif (Validator::getFileError()) {
            $this->data_error = Validator::getFileError();
            return false;
        } elseif ($filename) {
            $this->foto_detalle_zapato = $filename;
            return true;
        } else {
            $this->foto_detalle_zapato = 'default.png';
            return true;
        }
    }
    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }

    public function setNombreColor($value, $min = 2, $max = 20)
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
            $this->nombre_color = $value;
            return true;
        }
    }

    public function setDescripcion($value, $min = 2, $max = 200)
    {
        if (!Validator::validateAlphabetic($value)) {
            $this->data_error = 'La descripcion debe ser un valor alfabético'  ;
            return false;
        } elseif (!Validator::validateLength($value, $min, $max)) {
            $this->data_error = 'El nombre debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        } else {    
            $this->descripcion_zapato = $value;
            return true;
        }
    }

        // Método para establecer el nombre del trabajador
        public function setNombreZapato($value, $min = 2, $max = 20)
        {
            if (!Validator::validateAlphabetic($value)) {
                $this->data_error = 'El nombre debe ser un valor alfabético tu mami';
                return false;
            } elseif (Validator::validateLength($value, $min, $max)) {
                $this->nombre_zapato = $value;
                return true;
            } else {
                $this->data_error = 'El nombre debe tener una longitud entre tu mami ' . $min . ' y ' . $max;
                return false;
            }
        }

    public function setFilename()
    {
        if ($data = $this->readFilename()) {
            $this->filename = $data['foto_detalle_zapato'];
            return true;
        } else {
            $this->data_error = 'Zapato inexistente';
            return false;
        }
    }

    public function getFilename()
    {
        return $this->filename;
    }
}