<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DocumentoValido implements Rule
{
    protected $tipoDocumento;
    protected $errorMessage;

    public function __construct($tipoDocumento)
    {
        $this->tipoDocumento = $tipoDocumento;
    }

    public function passes($attribute, $value)
    {
        // Asegurarse de que sea solo números
        if (!preg_match('/^\d+$/', $value)) {
            $this->errorMessage = 'El número de documento debe contener solo dígitos.';
            return false;
        }

        // Filtro Anti-Basura
        // Bloquear todos los caracteres iguales (ej. 111111)
        if (preg_match('/^(\d)\1+$/', $value)) {
            $this->errorMessage = 'El documento ingresado parece ser inválido (secuencia repetitiva).';
            return false;
        }

        // Bloquear ascendentes o descendentes básicos (ej 123456, 987654)
        if (strlen($value) >= 6 && (str_contains('0123456789', $value) || str_contains('9876543210', $value))) {
            $this->errorMessage = 'El documento ingresado parece ser inválido (secuencia directa).';
            return false;
        }

        switch ($this->tipoDocumento) {
            case 'CC':
                $val = (int) $value;
                if (!(($val >= 20000000 && $val <= 80000000) || ($val >= 1000000000 && $val <= 1400000000))) {
                    $this->errorMessage = 'El número de Cédula no corresponde a los rangos válidos para adultos.';
                    return false;
                }
                break;

            case 'NIT':
                if (strlen($value) < 8 || strlen($value) > 11) {
                    $this->errorMessage = 'La longitud del NIT es inválida (debe incluir el dígito de verificación al final).';
                    return false;
                }
                if (!$this->validarModulo11($value)) {
                    $this->errorMessage = 'El NIT no supera la validación del dígito de verificación (Módulo 11).';
                    return false;
                }
                break;

            case 'PPT':
                if (strlen($value) < 7 || strlen($value) > 9) {
                    $this->errorMessage = 'El Permiso de Protección Temporal (PPT) debe tener entre 7 y 9 dígitos.';
                    return false;
                }
                break;

            case 'CE':
                if (strlen($value) < 4 || strlen($value) > 10) {
                     $this->errorMessage = 'La Cédula de Extranjería debe tener entre 4 y 10 dígitos.';
                     return false;
                }
                break;

            default:
                if (strlen($value) < 5) {
                    $this->errorMessage = 'El número de documento es demasiado corto.';
                    return false;
                }
                break;
        }

        return true;
    }

    public function message()
    {
        return $this->errorMessage ?: 'El número de documento no es válido.';
    }

    private function validarModulo11($nitFull)
    {
        $baseNit = substr($nitFull, 0, -1);
        $dvIngresado = (int) substr($nitFull, -1);

        $weights = [3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71];
        $sum = 0;
        $len = strlen($baseNit);

        for ($i = 0; $i < $len; $i++) {
            $digit = (int) $baseNit[$len - 1 - $i];
            $weight = $weights[$i] ?? 0;
            $sum += $digit * $weight;
        }

        $residuo = $sum % 11;
        
        if ($residuo > 1) {
            $dvEsperado = 11 - $residuo;
        } else {
            $dvEsperado = $residuo; 
        }

        return $dvIngresado === $dvEsperado;
    }
}
