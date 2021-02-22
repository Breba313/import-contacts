@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Load Files -->
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h3>Cargar Archivos</h3></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if(isset($errors)  && count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('load_file') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="file" class="col-md-4 col-form-label text-md-right">Archivo</label>
                            <div class="col-md-6">
                                <input type="file" id="file" name="file" class="form-control" aria-label="Seleccione" required>
                                <div class="invalid-feedback">Suba un archivo por favor</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-12 col-form-label text-md-center">A continuación, ingrese los nombres de las columnas del archivo correspondientes al campo señalado.</label>
                        </div>
                        <div class="form-group row">
                            <label for="field_name" class="col-md-4 col-form-label text-md-right"> Columna nombre</label>

                            <div class="col-md-4">
                                <input id="field_name" type="text" class="form-control" name="field_name" value="{{ old('field_name') }}" required autofocus>

                                @error('field_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="field_birthday" class="col-md-4 col-form-label text-md-right"> Columna fecha nacimiento</label>

                            <div class="col-md-4">
                                <input id="field_birthday" type="text" class="form-control" name="field_birthday" value="{{ old('field_birthday') }}" required autofocus>

                                @error('field_birthday')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="field_phone" class="col-md-4 col-form-label text-md-right"> Columna teléfono</label>

                            <div class="col-md-4">
                                <input id="field_phone" type="text" class="form-control" name="field_phone" value="{{ old('field_phone') }}" required autofocus>

                                @error('field_phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="field_address" class="col-md-4 col-form-label text-md-right"> Columna dirección</label>

                            <div class="col-md-4">
                                <input id="field_address" type="text" class="form-control" name="field_address" value="{{ old('field_address') }}" required autofocus>

                                @error('field_address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="field_credit_card_number" class="col-md-4 col-form-label text-md-right"> Columna numero tarjeta de credito</label>

                            <div class="col-md-4">
                                <input id="field_credit_card_number" type="text" class="form-control" name="field_credit_card_number" value="{{ old('field_credit_card_number') }}" required autofocus>

                                @error('field_credit_card_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="field_email" class="col-md-4 col-form-label text-md-right"> Columna email</label>

                            <div class="col-md-4">
                                <input id="field_email" type="text" class="form-control" name="field_email" value="{{ old('field_email') }}" required autofocus>

                                @error('field_email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button class="btn btn-primary" type="submit">Enviar!</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </br></br>
    <!-- Files list -->
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h3>Archivos cargados</h3></div>
                <div class="card-body">
                    <table id="datatable_files" class="table-datatables table table-hover table-striped no-margin responsive" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>Nombre del archivo</th>
                            <th>Fecha de creación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($files as $file)
                            <tr>
                                <td>{{ $file->filename }}</td>
                                <td>{{ $file->created_at }}</td>
                                <td>{{ $file->status }}</td>
                                <td>
                                
                                    <a class="btn btn-xs btn-success" href="{{ route('import', ['id_file' => $file->id] ) }}"> Importar </a>
                                
                                    <a id="btn_eliminar" class="btn btn-xs btn-danger" onclick="check_delete({{ $file->id }})"> Eliminar </a>
                                    <input type="hidden" name="url_delete" id="url_delete" value="{{ url('/delete_file/') }}/">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </br></br>
    <!-- Contact list -->
    <div class="row justify-content-center">
        <!-- List contacts -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h3>Contacts list</h3></div>

                <div class="card-body">
                    @if (session('status_c'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status_c') }}
                        </div>
                    @endif
                    @if (session('error_c'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error_c') }}
                        </div>
                    @endif

                    <table id="datatable_contacts" class="table-datatables table table-hover table-striped no-margin responsive" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Fecha Nacimiento</th>
                                <th>Telefono</th>
                                <th>Dirección</th>
                                <!-- <th>Tarjeta Credito</th> -->
                                <th>Franquicia TC</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contacts as $contact)
                                <tr>
                                <td>{{ $contact->name }}</td>
                                <td>{{ date('Y M d', strtotime($contact->birthday)) }}</td>
                                <td>{{ $contact->phone }}</td>
                                <td>{{ $contact->address }}</td>
                                <!-- <td>{{ $contact->credit_card_number }}</td> -->
                                <td>{{ $contact->franchise }}</td>
                                <td>{{ $contact->email }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('custom_jscripts')
    <script src="{{ asset('js/general.js') }}"></script>
@endsection
