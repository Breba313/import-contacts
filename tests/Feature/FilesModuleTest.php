<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Models\File;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FilesModuleTest extends TestCase
{
    /**
     * test load file
     *
     * @return void
     */
    use RefreshDatabase;

    private $user;
    private $file;

    private function create_user() {
        // Create a user
        return $user = factory(User::class)->create();

    }

    private function create_file($user_id) {
        // Create a file
        return $file = factory(File::class)->create([
            'user_id' => $user_id,
        ]);
        
    }

    private function create_file_other_columns($user_id) {
        // Create a file
        return $file = factory(File::class)->create([
            'user_id' => $user_id,
            'field_name' => 'alias',
        ]);
        
    }

    private function create_file_other_location($user_id) {
        // Create a file
        return $file = factory(File::class)->create([
            'user_id' => $user_id,
            'location' => '2/file.csv',
        ]);
        
    }

    public function getFile($url)
        {
            //get name file by url and save in object-file
            $path_parts = pathinfo($url);
            //get image info (mime, size in pixel, size in bits)
            $newPath = $path_parts['dirname'] . '/tmp-files/';
            if(!is_dir ($newPath)){
                Storage::disk('local')->makeDirectory('tmp-files');
            }
            $newUrl = $newPath . $path_parts['basename'];
            copy($url, $newUrl);
            $imgInfo = filesize($newUrl);
            $file = new UploadedFile(
                $newUrl,
                $path_parts['basename'],
                'csv',
                filesize($url),
                TRUE,
            );
            return $file;
        }
   

    public function test_user_can_load_file()
    {
        // Create user
        $this->user = $this->create_user();
        
        // Save the file 
        $url = storage_path('app/contacts.csv');
        $file = $this->getFile($url);
      
        $response = $this->actingAs($this->user)->json('POST', '/load_file', [
            'file' => $file,
            'field_name' => 'name',
            'field_birthday' => 'birthday',
            'field_phone' => 'phone',
            'field_address' => 'address',
            'field_credit_card_number' => 'creditcard',
            'field_email' => 'email',
         ]);
    
        // Assert the file exits
        Storage::disk('local')->assertExists($file->getClientOriginalName());
        
        $response->assertStatus(302);
        $response->assertRedirect('/home');

        // Assert the model file is create succesfully
        $response_file = $this->actingAs($this->user)->json('GET', '/home');
        $response_file->assertSee($file->getClientOriginalName());
        $response_file->assertSee('En espera');
        $response_file->assertSee('Se subió el archivo satisfactoriamente.');
    }

    public function test_user_cannot_load_file_extension_not_allowed()
    {
        // Create user
        $this->user = $this->create_user();

        // Create fake file with extension not allowed
        $file = UploadedFile::fake()->image('file.jpg');
        $response = $this->actingAs($this->user)->json('POST', '/load_file', [
            'file' => $file,
            'field_name' => 'name',
            'field_birthday' => 'birthday',
            'field_phone' => 'phone',
            'field_address' => 'address',
            'field_credit_card_number' => 'creditcard',
            'field_email' => 'email',
         ]);
    
        // Assert the file missing
        Storage::disk('local')->assertMissing($file->getClientOriginalName());
        $response->assertStatus(302);
        $response->assertRedirect('/home');

        // Assert message file not allowed
        $response_file = $this->actingAs($this->user)->json('GET', '/home');
        $response_file->assertSee('Extension de archivo no permitida.');
    }

    public function test_user_cannot_load_file_duplicate_file()
    {
        // Create user
        $this->user = $this->create_user();

        // Save file previously
        $url = storage_path('app/contacts.csv');
        $file = $this->getFile($url);
      
        $response = $this->actingAs($this->user)->json('POST', '/load_file', [
            'file' => $file,
            'field_name' => 'name',
            'field_birthday' => 'birthday',
            'field_phone' => 'phone',
            'field_address' => 'address',
            'field_credit_card_number' => 'creditcard',
            'field_email' => 'email',
         ]);

        // Validate name file
        $url2 = storage_path('app/contacts.csv');
        $file2 = $this->getFile($url2);
      
        $response2 = $this->actingAs($this->user)->json('POST', '/load_file', [
            'file' => $file2,
            'field_name' => 'name',
            'field_birthday' => 'birthday',
            'field_phone' => 'phone',
            'field_address' => 'address',
            'field_credit_card_number' => 'creditcard',
            'field_email' => 'email',
         ]);
    
        // Assert the filename exits
        $response2->assertStatus(302);
        $response2->assertRedirect('/home');

        $response2_file = $this->actingAs($this->user)->json('GET', '/home');
        $response2_file->assertSee('Ya se encuentra cargado este archivo.');
    }

    public function test_user_can_import_file()
    {
        // Create user
        $this->user = $this->create_user();
       
        // Create file
        $this->file = $this->create_file($this->user->id);
        
        // Save the file 
        $url = storage_path('app/contacts.csv');
        $file = $this->getFile($url);
     
        $response_import = $this->actingAs($this->user)->json('GET', '/import/'.$this->file->id);
         
        $response_import->assertStatus(302);
        $response_import->assertRedirect('/home');

        $response2_import = $this->actingAs($this->user)->json('GET', '/home');
        $response2_import->assertSee('Contactos importados satisfactoriamente');
    }

    public function test_user_cannot_import_file()
    {
        // Create user
        $this->user = $this->create_user();
       
        // Create file
        $this->file = $this->create_file_other_columns($this->user->id);
        
        // Save the file 
        $url = storage_path('app/contacts.csv');
        $file = $this->getFile($url);
     
        $response_import = $this->actingAs($this->user)->json('GET', '/import/'.$this->file->id);
         
        $response_import->assertStatus(302);
        $response_import->assertRedirect('/home');

        $response2_import = $this->actingAs($this->user)->json('GET', '/home');
        $response2_import->assertSee('Ha ocurrido un error durante la importación');
    }

    public function test_user_cannot_exist_file()
    {
        // Create user
        $this->user = $this->create_user();
       
        // Create file
        $this->file = $this->create_file($this->user->id);
        
        // Save the file 
        $url = storage_path('app/contacts.csv');
        $file = $this->getFile($url);
     
        $response_import = $this->actingAs($this->user)->json('GET', '/import/13');
         
        $response_import->assertStatus(302);
        $response_import->assertRedirect('/home');

        $response2_import = $this->actingAs($this->user)->json('GET', '/home');
        $response2_import->assertSee('El archivo no existe');
    }

    public function test_user_cannot_find_file()
    {
        // Create user
        $this->user = $this->create_user();
       
        // Create file
        $this->file = $this->create_file_other_location($this->user->id);
        
        // Save the file 
        $url = storage_path('app/contacts.csv');
        $file = $this->getFile($url);

        $response_import = $this->actingAs($this->user)->json('GET', '/import/'.$this->file->id);
         
        $response_import->assertStatus(302);
        $response_import->assertRedirect('/home');

        $response2_import = $this->actingAs($this->user)->json('GET', '/home');
        $response2_import->assertSee('El archivo no ha sido encontrado.');
    }

    public function test_user_can_delete_file()
    {
        // Create user
        $this->user = $this->create_user();
       
        // Create file
        $this->file = $this->create_file($this->user->id);
        
        $response_import = $this->actingAs($this->user)->json('GET', '/delete_file/'.$this->file->id);
         
        $response_import->assertJson(['success' => 'success'], 200);
    }

    public function test_user_cannot_delete_file()
    {
        // Create user
        $this->user = $this->create_user();
       
        // Create file
        $this->file = $this->create_file($this->user->id);
        
        $response_import = $this->actingAs($this->user)->json('GET', '/delete_file/a');
         
        $response_import->assertJson(['error' => 'invalid'], 401);
    }
}
