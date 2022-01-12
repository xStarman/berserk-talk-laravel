<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Developers;
class Developer extends Controller
{
    private $rules = [
        'nome' => 'required',
        'sexo' => 'required|in:M,F,O',
        'hobby_id' => 'required',
        'datanascimento' => 'required|date|before:today'
    ];

    private $messages = [
        'nome.required' => "O nome é obrigatório.",
        'sexo.required' => "O sexo é obrigatório.",
        'sexo.in' => "O valor informado é inválido.",
        'hobby_id.required' => "O hobby é obrigatório.",
        'datanascimento.required' => "A data de nascimento é obrigatória.",
        'datanascimento.date' => "Formato de data inválido.",
        'datanascimento.before' => "A data de nascimento precisa ser inferior à hoje."
    ];
    public function createDeveloper(Request $request)
    {
        $requestData = $request->all();
        $validator = \Validator::make($requestData, $this->rules, $this->messages);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $developer = new Developers();
        $developer->fill($requestData);
        $developer->setIdadeAttribute();
        $developer->save();

        return response()->json($developer, 201);
    }

    public function getDevelopers(){
        $developers = Developers::with('hobby')->get();
        return response()->json($developers, 200);
    }
}
