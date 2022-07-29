<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Article::all();
        return response()->json([
            "message" => "Get successfully.",
            "data" => $books
        ]);
    }

    public function store(Request $request)
    {
        $book = new Article;
        $book->name = $request->name;
        $book->author = $request->author;
        $book->publish_date = $request->publish_date;
        $book->save();
        return response()->json([
            "message" => "Book Added."
        ], 201);
    }

    public function show($id)
    {
        $book = Article::find($id);
        if(!empty($book))
        {
            return response()->json($book);
        }
        else
        {
            return response()->json([
                "message" => "Book not found"
            ], 404);
        }
    }

    public function update(Request $request)
    {
        $id = $request->id;
        if (is_null($id)) {
            return response()->json([
               "message" => "id is required."
            ], 200);
        }
        $article = Article::find($id);
        if (!empty($article)) {
//            $book = Article::find($id);
//            $book->name = is_null($request->name) ? $book->name : $request->name;
//            $book->author = is_null($request->author) ? $book->author : $request->author;
//            $book->publish_date = is_null($request->publish_date) ? $book->publish_date : $request->publish_date;
//            $book->save();
            $article->update($request->all());
            return response()->json([
               "message" => "Book Updated."
            ], 200);
        } else {
            return response()->json([
                "message" => "Book Not Found."
            ], 200);
        }
    }

    public function destroy($id)
    {
        if (Article::where('id', $id)->exist()) {
            $book = Article::find($id);
            $book->delete();

            return response()->json([
                "message" => "records deleted."
            ], 202);
        } else {
            return response()->json([
                "message" => "book not found."
            ], 404);
        }
    }
}
