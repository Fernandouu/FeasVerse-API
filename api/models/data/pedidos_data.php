

<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/pedidos_handler.php');

/*
*	Clase para manejar el encapsulamiento de los datos de la tabla CLIENTE.
*/
class PedidosData extends PedidosHandler
{
    // Atributo genérico para manejo de errores.
    private $data_error = null;

    /*
    *   Métodos para validar y establecer los datos.
    */

    // Método para establecer el ID del pedido del cliente.
    public function setIdPedidoCliente($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_pedido_cliente = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del pedido del cliente es incorrecto';
            return false;
        }
    }

    // Método para establecer el ID del cliente.
    public function setIdCliente($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_cliente = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del cliente es incorrecto';
            return false;
        }
    }

    // Método para establecer el ID del repartidor.
    public function setIdRepartidor($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_repartidor = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del repartidor es incorrecto';
            return false;
        }
    }

    // Método para establecer el estado del pedido.
    public function setEstadoPedido($value)
    {
        if (Validator::validateAlphabetic($value)) {
            $this->estado_pedido = $value;
            return true;
        } else {
            // Si la validación falla
            $this->data_error = 'Ha ocurrido un error: El valor proporcionado no es válido';
            return false;
        }
    }

    // Método para establecer el estado del pedido usando valores numéricos.
    public function setEstadoPedido2($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            if ($value == 1) {
                $this->estado_pedido = 'Pendiente';
                return true;
            } elseif ($value == 2) {
                $this->estado_pedido = 'En camino';
                return true;
            } elseif ($value == 3) {
                $this->estado_pedido = 'Entregado';
                return true;
            } elseif ($value == 4) {
                $this->estado_pedido = 'Carrito';
                return true;
            } else {
                // Si la validación falla o el valor no coincide con 1, 2 o 3
                $this->data_error = 'Ha ocurrido un error: El valor proporcionado no es válido';
                return false;
            }
        } else {
            // Si la validación falla o el valor no es numérico
            $this->data_error = 'Ha ocurrido un error: El valor proporcionado no es válido';
            return false;
        }
    }

    // Método para establecer el precio total del pedido.
    public function setPrecioTotal($value)
    {
        if (Validator::validateMoney($value)) {
            $this->precio_total = $value;
            return true;
        } else {
            $this->data_error = 'El precio total debe ser un número válido';
            return false;
        }
    }

    // Método para establecer la fecha de inicio del pedido.
    public function setFechaDeInicio($value)
    {
        if (Validator::validateDate($value)) {
            $this->fecha_de_inicio = $value;
            return true;
        } else {
            $this->data_error = 'La fecha de inicio es incorrecta';
            return false;
        }
    }

    // Método para establecer la fecha de entrega del pedido.
    public function setFechaDeEntrega($value)
    {
        if (Validator::validateDate($value)) {
            $this->fecha_de_entrega = $value;
            return true;
        } else {
            $this->data_error = 'La fecha de entrega es incorrecta';
            return false;
        }
    }

    // Método para establecer el ID del costo de envío por departamento.
    public function setIdCostoDeEnvioPorDepartamento($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_costo_de_envio_por_departamento = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del costo de envío por departamento es incorrecto';
            return false;
        }
    }

    // Método para establecer el ID de los detalles del pedido.
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

    // Método para establecer el ID del detalle del zapato.
    public function setIdDetalleZapato($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_detalle_zapato = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del detalle del zapato es incorrecto';
            return false;
        }
    }

    // Método para establecer la cantidad del pedido.
    public function setCantidadPedido($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->cantidad_pedido = $value;
            return true;
        } else {
            $this->data_error = 'La cantidad del pedido es incorrecta';
            return false;
        }
    }

    // Método para establecer el precio del zapato.
    public function setPrecioDelZapato($value)
    {
        if (Validator::validateMoney($value)) {
            $this->precio_del_zapato = $value;
            return true;
        } else {
            $this->data_error = 'El precio del zapato debe ser un número válido';
            return false;
        }
    }

    public function setIdComentario($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_comentario = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del comentario es incorrecto';
            return false;
        }
    }

    public function setEstadoComentario($value)
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

    public function setDescripcionComentario($value, $min = 2, $max = 250)
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

    public function setTituloComentario($value, $min = 2, $max = 50)
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
    
    public function setCalificacionComentario($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->calificacion_comentario = $value;
            return true;
        } else {
            $this->data_error = 'El valor de la calificacion debe ser numérico entero';
            return false;
        }
    }

    public function setFechaComentario($value)
    {
        if (Validator::validateDate($value)) {
            $this->fecha_del_comentario = $value;
            return true;
        } else {
            $this->data_error = 'La fecha del comentario es incorrecta';
            return false;
        }
    }

    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}