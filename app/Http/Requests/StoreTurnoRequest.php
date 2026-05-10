<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DocumentoValido;

class StoreTurnoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $numeroDocumento = $this->input('numero_documento');
        $telefono = $this->input('telefono');

        if ($numeroDocumento) {
            // Eliminar puntos, comas, guiones y espacios
            $numeroDocumento = preg_replace('/[\.\,\-\s]/', '', $numeroDocumento);
            $this->merge(['numero_documento' => $numeroDocumento]);
        }

        if ($telefono) {
            $telefono = preg_replace('/[\.\,\-\s]/', '', $telefono);
            $this->merge(['telefono' => $telefono]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tipoDoc = $this->input('pers_tipodoc');

        return [
            'tipo_atencion'    => 'required|string',
            'pers_tipodoc'     => 'required|string',
            'numero_documento' => [
                'required',
                'string',
                new DocumentoValido($tipoDoc),
            ],
            'pers_nombres'     => 'nullable|string',
            'pers_apellidos'   => 'nullable|string',
            'telefono'         => 'nullable|numeric|digits_between:7,15',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tipo_atencion.required'    => 'Debe seleccionar un tipo de atención.',
            'pers_tipodoc.required'     => 'El tipo de documento es obligatorio.',
            'numero_documento.required' => 'Por favor, ingrese su número de documento.',
            'numero_documento.string'   => 'El formato del documento no es válido.',
            'telefono.numeric'          => 'El teléfono ingresado debe contener solo números.',
            'telefono.digits_between'   => 'El teléfono debe tener entre 7 y 15 dígitos.',
        ];
    }
}
