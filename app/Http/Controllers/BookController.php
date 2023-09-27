<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::all();
        return response()->json($books, 200);
    }

    public function show($id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }
        return response()->json($book, 200);
    }

    public function store(Request $request)
    {
        $book = Book::create($request->all());
        return response()->json($book, 201);
    }

    public function update(Request $request, $id)
    {
        $book = Book::find($id);
        $book->update($request->all());
        return response()->json($book, 200);
    }

    public function destroy($id)
    {
        $book = Book::find($id);
        $book->delete();
        return response()->json($book, 204);
    }
}
