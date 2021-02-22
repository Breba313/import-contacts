<?php

namespace App\Http\Controllers;

use App\Imports\ContactsImport;
use App\Models\File;
use Illuminate\Http\Request;
use Storage;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class FilesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Guarda un archivo
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadFile(Request $request)
    {
        //obtenemos el campo file definido en el formulario
        $file = $request->file('file');
        $data = $request->all();
        //obtenemos el nombre del archivo
        $filename  = $file->getClientOriginalName();
        $extension  = $file->getClientOriginalExtension();

        if (!$this->validatorFile($request)) {
            return redirect('/home')->with('error', 'Extension de archivo no permitida.');
        }

        //  Verificamos que el archivo no exista en la base de datos
        if ($this->validatorFileName($filename)) {
            return redirect('/home')->with('error', 'Ya se encuentra cargado este archivo.');
        }

        //  Guardamos el archivo
        \Storage::disk('local')->put(Auth::user()->id."/".$filename,  \File::get($file));
    
        //  Preparamos la data para guardar en la tabla files
        $data['user_id']= Auth::user()->id;
        $data['filename'] = $filename;
        $data['location']= Auth::user()->id.'/'.$filename;
        
        //  Creamos el registro en la base de datos
        $this->createFile($data);

        return redirect('/home')->with('status', 'Se subió el archivo satisfactoriamente.');
    }

     /**
     * Obtiene el validator para los archivos a subir
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validatorFile(Request $request)
    {
        //  Derfinimos los tipos de archivos permitidos
        $array_ext = ['csv', 'xlsx', 'xlsx'];

        $file = $request->file('file');
        $extension  = $file->getClientOriginalExtension();
        if (in_array($extension, $array_ext)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Validar que el nombre del archivo exista en la base de datos
     *
     * @param  string  $filename
     * @return boolean
     */
    protected function validatorFileName($filename){
        if ( File::select("id")->where("filename", $filename)->where("user_id", Auth::id())->get()->count() > 0) {
            return true;
        }
        return false;
    }

     /**
     * Crea un nuevo registro del archivo guardado
     *
     * @param  array  $data
     * @return file
     */
    protected function createFile(array $data)
    {
        return File::create([
            'user_id' => $data['user_id'],
            'filename' => $data['filename'],
            'location' => $data['location'],
            'field_name' => $data['field_name'],
            'field_birthday' => $data['field_birthday'],
            'field_phone' => $data['field_phone'],
            'field_address' => $data['field_address'],
            'field_credit_card_number' => $data['field_credit_card_number'],
            'field_email' => $data['field_email'],
        ]);
    }

    /**
     * Procesa el archivo e importa los contactos
     *
     * @return \Illuminate\Http\Response
     */
    public function importFile($id_file){
        $file = File::find($id_file); 
        $locationFile = storage_path("app")."/".$file->location;
        if ($file) {
            if (Storage::disk('local')->exists($file->location)) {
                try {
                    $import = new ContactsImport;
                    $import->setColName($file->field_name);
                    $import->setColBirthday($file->field_birthday);
                    $import->setColPhone($file->field_phone);
                    $import->setColAddress($file->field_address);
                    $import->setColCreditCardNumber($file->field_credit_card_number);
                    $import->setColEmail($file->field_email);
                    $this->updateStatusFile($id_file, 'Procesando');
                    Excel::import($import, $locationFile);
                    if ($import->getRowCount() > 0){
                        $this->updateStatusFile($id_file, 'Terminado');
                        $errors = $import->getErrors();
                        if ($errors) {
                            return redirect('/home')->with('status_c', $import->getRowCount().' Contactos importados satisfactoriamente.')->with('error_c', 'Ha ocurrido un error durante la importación: '. json_encode($errors));
                        } else {
                            return redirect('/home')->with('status_c', $import->getRowCount().' Contactos importados satisfactoriamente.');
                        }
                    } else {
                        $errors = $import->getErrors();
                        return redirect('/home')->with('error_c', 'Ha ocurrido un error durante la importación: '. json_encode($errors));
                    }
                    
                } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                    $failures = $e->failures();
                    // Guardamos la info en el log
                    Log::info($e->failures());

                    foreach ($failures as $failure) {
                        $failure->row(); // row that went wrong
                        $failure->attribute(); // either heading key (if using heading row concern) or column index
                        $failure->errors(); // Actual error messages from Laravel validator
                        $failure->values(); // The values of the row that has failed.
                    }
                    $this->updateStatusFile($id_file, 'Fallido');
                    return redirect('/home')->with('error_c', 'Ha ocurrido un error durante la importación.')->with('errors', $failure->errors());
                } 
                
            }
        }
       
    }

    /**
     * Actualiza el estadu del archivo
     *
     * @return \Illuminate\Http\Response
     */
    private function updateStatusFile($id_file, $status)
    {
        $file = File::find($id_file); 
        if ($file) {
            $file->status = $status;
            $file->save();
        }            
    }

    /**
     * Elimina el archivo
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteFile($id_file)
    {
        $response = response()->json(['error' => 'invalid'], 401);
        $file = File::find($id_file); 
        if ($file) {
            if (Storage::disk('local')->exists(Auth::user()->id. "/" .$file->filename)) {
                if (Storage::disk('local')->delete(Auth::user()->id. "/" .$file->filename)) {
                    $file->Delete();    
                    $response = response()->json(['success' => 'success'], 200);
                } 
            }
        }            
        return $response;
    }
}
