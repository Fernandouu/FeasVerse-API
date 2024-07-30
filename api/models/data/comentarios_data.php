<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/comentarios_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla CATEGORIA.
 */

class ComentariosData extends ComentariosHandler{

    private $data_error = null;

    /*
     *  Métodos para obtener los atributos adicionales.
     */
    public function getDataError()
    {
        return $this->data_error;
    }

    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_comentario = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del comentario es incorrecto';
            return false;
        }
    }

    public function setEstado($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            if ($value == 1) {
                $this->estado_comentario = 'Activo';
                return true;
            } elseif ($value == 2) {
                $this->estado_comentario = 'Desactivo';
                return true;
            }
            else{    
                // Si la validación falla o el valor no coincide con 1 o 2
                $this->data_error = 'Ha ocurrido un error: El valor proporcionado no es válido';
                return false;
            }
            
        }
        else{    
            // Si la validación falla o el valor no coincide con 1 o 2
            $this->data_error = 'Ha ocurrido un error: El valor proporcionado no es válido';
            return false;
        }
    }

    public function setDescripcion($value, $min = 2, $max = 250)
    {
        if (!Validator::validateString($value)) {
            $this->data_error = 'La descripción contiene caracteres prohibidos';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->descripcion_comentario = $value;
            return true;
        } else {
            $this->data_error = 'La descripción debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    public function setTitulo($value, $min = 2, $max = 50)
    {
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'El titulo debe ser un valor alfanumérico';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->titulo_comentario = $value;
            return true;
        } else {
            $this->data_error = 'El titulo debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    
    public function setCalificacion($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->calificacion_comentario = $value;
            return true;
        } else {
            $this->data_error = 'El valor de la calificacion debe ser numérico entero';
            return false;
        }
    }

    public function setFecha($value)
    {
        if (Validator::validateDate($value)) {
            $this->fecha_del_comentario = $value;
            return true;
        } else {
            $this->data_error = 'La fecha del comentario es incorrecta';
            return false;
        }
    }

    public function setIdDetallesPedido($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_detalles_pedido = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de los detalles del pedido es incorrecto';
            return false;
        }
    
    }
}

