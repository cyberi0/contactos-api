<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Direccion;
use App\Models\Telefono;
use App\Models\Correo;
use Illuminate\Http\Request;

class ContactoController extends Controller {

    /**
     * Inserta Contactos con sus Referencias: Dirección(es), Teléfono(s) y Correo(s)
     * @param json |$request
     * @return json
     */
    public function register(Request $request) {
        $validationResult = $this->validateContacto($request);
        if (!$validationResult['success']) {
            return response()->json(['errors' => $validationResult['errors']], 420);
        }

        $nombre = $this->sanitizeRequest($request->input('nombre'));

        $contacto = Contacto::create([
            'nombre' => $nombre
        ]);

        if ($request->has('direcciones')) {
            foreach ($request->input('direcciones') as $direccionData) {
                $direccionData = $this->sanitizeRequest($direccionData);
                $direccion = new Direccion($direccionData);
                $contacto->direcciones()->save($direccion);
            }
        }

        if ($request->has('telefonos')) {
            foreach ($request->input('telefonos') as $telefonoData) {
                $telefonoData = $this->sanitizeRequest($telefonoData);
                $telefono = new Telefono($telefonoData);
                $contacto->telefonos()->save($telefono);
            }
        }

        if ($request->has('correos')) {
            foreach ($request->input('correos') as $correoData) {
                $correoData = $this->sanitizeRequest($correoData);
                $correo = new Correo($correoData);
                $contacto->correos()->save($correo);
            }
        }

        return response()->json(['message' => 'Contacto Registrado'], 200);
    }

    /**
     * Actualización de Contactos mediante ID, devolviendo el Detalle [Dirección(es), Teléfono(s) y Correo(s)]
     * @param json |$request
     * @param number |$id
     * @return json
     */
    public function update(Request $request, $id) {
        $contacto = Contacto::findOrFail($id);
        $contacto->nombre = $request->input('nombre');
        $contacto->save();

        if ($request->has('direcciones')) {
            $contacto->direcciones()->delete();
            foreach ($request->input('direcciones') as $direccionData) {
                $direccion = new Direccion($direccionData);
                $contacto->direcciones()->save($direccion);
            }
        }

        if ($request->has('telefonos')) {
            $contacto->telefonos()->delete();
            foreach ($request->input('telefonos') as $telefonoData) {
                $telefono = new Telefono($telefonoData);
                $contacto->telefonos()->save($telefono);
            }
        }

        if ($request->has('correos')) {
            $contacto->correos()->delete();

            foreach ($request->input('correos') as $correoData) {
                $correo = new Correo($correoData);
                $contacto->correos()->save($correo);
            }
        }

        return response()->json(['message' => 'Contacto actualizado'], 200);
    }

    /**
     * Eliminación de Contactos mediante ID, borrando el Detalle [Dirección(es), Teléfono(s) y Correo(s)]
     * @param number |$id
     * @return json
     */
    public function delete($id) {
        $contacto = Contacto::findOrFail($id);
        $contacto->direcciones()->delete();
        $contacto->telefonos()->delete();
        $contacto->correos()->delete();
        $contacto->delete();
        return response()->json(['message' => 'Contacto Eliminado'], 200);
    }

    /**
     * Todos los Contactos ordenados alfabeticamente por nombre
     * @return json
     */
    public function getAll(Request $request) {
        $Contactos = Contacto::orderBy('nombre')->get();
        return response()->json($Contactos);
    }

    /**
     * Búsqueda de Contactos mediante ID, devolviendo el Detalle [Dirección(es), Teléfono(s) y Correo(s)]
     * @param number |$id
     * @return json
     */
    public function getById($id) {
        $Contacto = Contacto::with('direcciones', 'correos', 'telefonos')->find($id);
        if (!$Contacto) {
            return response()->json(['error' => 'No existe el Contacto'], 404);
        }
        return response()->json($Contacto);
    }

    /**
     * Búsqueda de Contactos mediante Nombre, Direccion, Teléfono y Correo
     * @param string |$request
     * @return json
     */
    public function filterContacts(Request $request) {
        $filtro = $request->input('filter');
        $filtro = trim($filtro);
        $filtro = htmlspecialchars($filtro, ENT_QUOTES);

        $contactos = Contacto::where(function ($query) use ($filtro) {
            $query->where('nombre','like',"%$filtro%");
            $query->orWhereHas('direcciones', function ($query) use ($filtro) {
                $query->where('calle','like',"%$filtro%")
                    ->orWhere('numero','like',"%$filtro%")
                    ->orWhere('codigo_postal','like',"%$filtro%");
            });
            $query->orWhereHas('telefonos', function ($query) use ($filtro) {$query->where('numero', 'like', "%$filtro%");});
            $query->orWhereHas('correos', function ($query) use ($filtro) {$query->where('correo', 'like', "%$filtro%");});
        })->orderBy('nombre')->get();

        return response()->json($contactos);
    }

    /**
     * Sanitiza los datos del request para prevenir inyecciones SQL y ataques XSS.
     */
    private function sanitizeRequest($input)
    {
        if (is_array($input)) {
            foreach ($input as &$value) { $value = $this->sanitizeRequest($value);}
            return $input;
        }

        if (is_string($input)) {
            $input = trim($input);
            $input = htmlspecialchars($input, ENT_QUOTES);
        }

        return $input;
    }

    /**
     * Validación de datos antes de insertar la información.
     */
    private function validateContacto(Request $request) {
        $rules = [
            'nombre' => 'required|string|max:255',
            'direcciones.*.calle' => 'required|string|max:255',
            'direcciones.*.numero' => 'required|string|max:50',
            'direcciones.*.codigo_postal' => 'required|string|max:10',
            'telefonos.*.numero' => 'required|string|max:20',
            'correos.*.correo' => 'required|email|max:255',
        ];

        $validator = validator()->make($request->all(), $rules);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ];
        }

        return [
            'success' => true
        ];
    }
}
