<?php

namespace App\Imports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Validator;

class ContactsImport implements ToModel, SkipsOnError, WithCustomCsvSettings
{
    use Importable;

    private $rows = 0;
    private $rows_imported = 0;
    private $arr_keys = [];
    private $errors = [];
    private $col_name;
    private $col_birthday;
    private $col_phone;
    private $col_address;
    private $col_credit_card_numbers;
    private $col_email;
  
    //  Setter para establecer la posicion del campo name
    public function setColName(string $col_name)
    {
        $this->col_name = $col_name;
    }

    //  Setter para establecer la posicion del campo fecha de nacimiento
    public function setColBirthday(string $col_birthday)
    {
        $this->col_birthday = $col_birthday;
    }

    //  Setter para establecer la posicion del campo telefono
    public function setColPhone(string $col_phone)
    {
        $this->col_phone = $col_phone;
    }

    //  Setter para establecer la posicion del campo direccion
    public function setColAddress(string $col_address)
    {
        $this->col_address = $col_address;
    }

    //  Setter para establecer la posicion del campo numero de tarjeta de credito
    public function setColCreditCardNumber(string $col_credit_card_numbers)
    {
        $this->col_credit_card_numbers = $col_credit_card_numbers;
    }

    //  Setter para establecer la posicion del campo email
    public function setColEmail(string $col_email)
    {
        $this->col_email = $col_email;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {      
        $contact = [];
        ++$this->rows;

       //  Validamos si es la primera fila de nombres de columnas
        if ($this->rows == 1) {
            // Establecemos el arreglo de llaves de campos
           $this->arr_keys = $row;
           
        } else {
            $contact = array_combine($this->arr_keys, $row);
            
            $validator = Validator::make($contact, [
                //$this->col_name =>'required|string|regex:/^[a-z 0-9_-]{3,16}$/',
                $this->col_name =>'required|string',
                $this->col_birthday => 'required|date_format:Y-m-d',
                $this->col_phone=> 'required',
                $this->col_address => 'required|string',
                $this->col_credit_card_numbers => 'required',
                $this->col_email =>'required|string|regex:/^.+@.+$/i|unique:users,email',
            ]);

            if ($validator->fails()) {
                $this->errors[] = $validator->Messages()->toArray();
                $this->errors[] = " En la fila numero: ". $this->rows;
                Log::info($validator->Messages()->toArray());
                return null;
            }           
            ++$this->rows_imported;
            $franchise = $this->getFranchise($contact[$this->col_credit_card_numbers]);
            return new Contact([
                'user_id' => Auth::user()->id,
                'name' => $contact[$this->col_name],
                'birthday' => $contact[$this->col_birthday],
                'phone' => $contact[$this->col_phone],
                'address' => $contact[$this->col_address],
                'credit_card_number' => $this->encoded($contact[$this->col_credit_card_numbers]),
                'franchise' => $franchise,
                'email' => $contact[$this->col_email],
            ]);            
    
        }
        

    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }
    
    public function getRowCount(): int
    {
        return $this->rows_imported;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param \Throwable $e
     */
    public function onError(\Throwable $e)
    {
        // Guardamos la info en el log
        Log::info($e->getMessage());
    }
    
    public static function getFranchise(int $credit_card_number = null)
    {
        $franchise = "";
        $franchise_regex = [
            'Visa' => '/^4[0-9]{6,}$/',
            'MasterCard' => '/^5[1-5][0-9]{5,}|222[1-9][0-9]{3,}|22[3-9][0-9]{4,}|2[3-6][0-9]{5,}|27[01][0-9]{4,}|2720[0-9]{3,}$/',
            'American Express' => '/^3[47][0-9]{5,}$/',
            'Diners Club' => '/^3(?:0[0-5]|[68][0-9])[0-9]{4,}$/',
            'Discover' => '/^6(?:011|5[0-9]{2})[0-9]{3,}$/',
            'JCB' => '/^(?:2131|1800|35[0-9]{3})[0-9]{3,}$/',
        ];
        foreach ($franchise_regex as $key_franchise => $pattern) {
            preg_match($pattern, $credit_card_number, $matches);   
            if (count($matches) > 0 ){
                $franchise = $key_franchise;
            }
        }
        return $franchise;
    }    

	/* Codifica un numero de tarjeta.
	 *
	 * @return string
	 */
	public function encoded($str, $estilo=FALSE) {
		$encoded= trim($str);
		//$num= mt_rand(2,5);
		$num=2;
		
		for($i=1; $i<=$num; $i++){
			$encoded= base64_encode($encoded);
		}
		
		$alpha_array= array('Y','C','D','A','U','R','Z','P','S','I','M','O');
		$encoded= $encoded."+".$alpha_array[$num];
		$encoded= base64_encode($encoded);
		return $encoded;
	}

     /**
	 * Decodifica un numero de tarjeta.
	 *
	 * @return string
	 */
	public function decoded($str) {
		$alpha_array= array('Y','C','D','A','U','R','Z','P','S','I','M','O');
		$decoded= base64_decode($str);
		list($decoded, $letter)= split("\+",$decoded);
		for($i=0;$i<count($alpha_array);$i++){
			if($alpha_array[$i] == $letter)
				break;
		}
		
		for($j=1;$j<=$i;$j++){ 
			$decoded= base64_decode($decoded);
		}
		return $decoded;
	} 
	
    
}
