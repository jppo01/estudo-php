<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PessoasController extends Controller
{
    public function index () {
        $peoples = [
                [
                    'img' => 'https://i.pravatar.cc/150?img='.rand(0,50),
                    'name' => 'Rafaela',
                    'age' => 55,
                    'birth' => '20/12/1968'
                ],
                [
                    'img' => 'https://i.pravatar.cc/150?img='.rand(0,50),
                    'name' => 'João',
                    'age' => 21,
                    'birth' => '20/12/2002'
                ],
                [
                    'img' => 'https://i.pravatar.cc/150?img='.rand(0,50),
                    'name' => 'Rayssa',
                    'age' => 70,
                    'birth' => '20/12/1955'
                ],
                [
                    'img' => 'https://i.pravatar.cc/150?img='.rand(0,50),
                    'name' => 'Suely',
                    'age' => 80,
                    'birth' => '20/12/1943'
                ],
                [
                    'img' => 'https://i.pravatar.cc/150?img='.rand(0,50),
                    'name' => 'Batista',
                    'age' => 90,
                    'birth' => '20/12/1933'
                ]
            ];
        $data['peoples'] = $peoples;
        return view('people', $data);

    }
}
