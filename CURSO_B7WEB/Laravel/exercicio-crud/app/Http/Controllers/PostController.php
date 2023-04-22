<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function Create(Request $r){
        $new_post = [
            'title' => 'Meu primeiro post',
            'content' => 'Bla bla bla bla bla bla bla',
            'author' => 'Joaozinho'
        ];

        $post = new Post($new_post);
        $post->save(); // Se tiro essa função pra não salvar o registo não dá erro
        dd($post);
    }

    public function Read(Request $r, $id){
        $post = new Post();
        $posts = $post->find($id); // procura por um id
        return $posts;
    }

    public function ReadAll(Request $r){
        $post = new Post();
        $posts = $post->all(); //puxa todos os posts

        return $posts;
    }

    public function Update(Request $r, $id){
        $post = new Post();
        $post_id = $post->find($id);
        $post_id -> title = 'Novo titulo';
        $post_id -> save();

        return $post_id;
    }

    public function MultUpdate(Request $r){

        $posts = Post::where('id', '>', 0)-> update([
            'author' => "desconhecido"
        ]);

        return $posts;

    }

    public function Delete(Request $r, $id)
    {
        $post = Post::find($id);
        if($post){
            $post->delete();
        }
        return $post;
        
    }
}
