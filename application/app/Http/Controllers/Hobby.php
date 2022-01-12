<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hobbies;
class Hobby extends Controller
{
    public function createHobby(Request $request)
    {
        $validator = \Validator::make($request->all(), ['name' => 'required'], ['name.required' => "O nome é obrigatório."]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $hobby = new Hobbies();
        $hobby->name = $request->name;
        $hobby->save();

        return response()->json($hobby, 201);
    }
}
