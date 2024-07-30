<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/trabajadores_handler.php');

/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla USUARIO.
 */
class TrabajadorData extends TrabajadorHandler
{
    // Atributo genérico para manejo de errores.
    private $data_error = null;

    /*
     *  Métodos para validar y asignar valores de los atributos.
     */

    // Método para establecer el ID del trabajador
    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_trabajador = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del trabajador es incorrecto';
            return false;
        }
    }

    // Método para establecer el nombre del trabajador
    public function setNombre($value, $min = 2, $max = 20)
    {
        if (!Validator::validateAlphabetic($value)) {
            $this->data_error = 'El nombre debe ser un valor alfabético';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->nombre_trabajador = $value;
            return true;
        } else {
            $this->data_error = 'El nombre debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    // Método para establecer el apellido del trabajador
    public function setApellido($value, $min = 2, $max = 20)
    {
        if (!Validator::validateAlphabetic($value)) {
            $this->data_error = 'El apellido debe ser un valor alfabético';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->apellido_trabajador = $value;
            return true;
        } else {
            $this->data_error = 'El apellido debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    // Método para establecer el DUI del trabajador
    public function setDUI($value)
    {
        if (!Validator::validateDUI($value)) {
            $this->data_error = 'El DUI debe tener el formato ########-#';
            return false;
        } elseif ($this->checkDuplicate($value)) {
            $this->data_error = 'El DUI ingresado ya existe';
            return false;
        } else {
            $this->dui_trabajador = $value;
            return true;
        }
    }

    // Método para establecer el teléfono del trabajador
    public function setTelefono($value)
    {
        if (Validator::validatePhone($value)) {
            $this->telefono_trabajador = $value;
            return true;
        } else {
            $this->data_error = 'El teléfono debe tener el formato (2, 6, 7)###-####';
            return false;
        }
    }

    // Método para establecer el correo del trabajador
    public function setCorreo($value, $min = 8, $max = 30)
    {
        if (!Validator::validateEmail($value)) {
            $this->data_error = 'El correo no es válido';
            return false;
        } elseif (!Validator::validateLength($value, $min, $max)) {
            $this->data_error = 'El correo debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        } elseif ($this->checkDuplicate($value)) {
            $this->data_error = 'El correo ingresado ya existe';
            return false;
        } else {
            $this->correo_trabajador = $value;
            return true;
        }
    }

    // Método para establecer el correo del trabajador sin validación de longitud
    public function setPasswordCorreo($value, $min = 8, $max = 30)
    {
        if (!Validator::validateEmail($value)) {
            $this->data_error = 'El correo no es válido';
            return false;
        } elseif (!Validator::validateLength($value, $min, $max)) {
            $this->data_error = 'El correo debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        } else {
            $this->correo_trabajador = $value;
            return true;
        }
    }

    // Método para establecer la contraseña del trabajador
    public function setClave($value)
    {
        if (Validator::validatePassword($value)) {
            $this->clave_trabajador = password_hash($value, PASSWORD_DEFAULT);
            return true;
        } else {
            $this->data_error = Validator::getPasswordError();
            return false;
        }
    }

    // Método para establecer la fecha de registro del trabajador
    public function setRegistro($value)
    {
        if (Validator::validateDate($value)) {
            $this->fecha_de_registro = $value;
            return true;
        } else {
            $this->data_error = 'La fecha de registro es incorrecta';
            return false;
        }
    }

    // Método para establecer la fecha de nacimiento del trabajador
    public function setNacimiento($value)
    {
        if (Validator::validateDate($value)) {
            $this->fecha_de_nacimiento = $value;
            return true;
        } else {
            $this->data_error = 'La fecha de nacimiento es incorrecta';
            return false;
        }
    }

    // Método para establecer el ID del nivel del trabajador
    public function setIdNivel($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_nivel = $value;
            return true;
        } else {
            $this->data_error = 'El nivel es incorrecto';
            return false;
        }
    }

    // Método para establecer el estado del trabajador
    public function setEstado($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            if ($value == 1) {
                $this->estado_trabajador = 'Activo';
                return true;
            } elseif ($value == 2) {
                $this->estado_trabajador = 'Desactivo';
                return true;
            } else {
                // Si la validación falla o el valor no coincide con 1 o 2
                $this->data_error = 'Ha ocurrido un error: El valor proporcionado no es válido';
                return false;
            }
        } else {
            // Si la validación falla o el valor no coincide con 1 o 2
            $this->data_error = 'Ha ocurrido un error: El valor proporcionado no es válido';
            return false;
        }
    }

    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}
