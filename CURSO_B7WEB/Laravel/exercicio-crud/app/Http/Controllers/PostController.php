<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function Create(Request $request){
        $new_post = [
            'title' => 'Meu primeiro post',
            'content' => 'Bla bla bla bla bla bla bla',
            'author' => 'Joaozinho'
        ];

        $post = new Post($new_post);
        $post->save(); // Se tiro essa função pra não salvar o registo não dá erro
        dd($post);
    }
}
