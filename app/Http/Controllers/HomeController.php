<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\File;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $files = $this->getFiles();
        $contacts = $this->getContacts();
        return view('home')
                            ->with('files', $files)
                            ->with('contacts', $contacts);
    }

     /**
     * Obtine el listado de archivos
     *
     * @return Object
     */
    public function getFiles()
    {
        // Obtenemos el listado de archivos del usuario
        $files = File::where('user_id', '=', Auth::user()->id)->get();
        return $files;
    }   
    
    /**
     * Obtiene el listado de contactos
     *
     * @return Object
     */
    public function getContacts()
    {
        // Obtenemos el listado de contactos del usuario
        $contacts = Contact::where('user_id', '=', Auth::user()->id)->get();
        return $contacts;
    }   
}
