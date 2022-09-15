<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use App\Http\Requests\TodoRequest;
use PhpParser\Node\Expr\FuncCall;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    protected $user;

    public function __construct()
    {
    }

    public function index()
    {
        $this->user = JWTAuth::parseToken()->authenticate();

        $todos = $this->user->todo()->get();

        if ($todos->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No todo found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'todos' => $todos
        ], 200);
    }

    public function store(TodoRequest $request)
    {

        $this->user = JWTAuth::parseToken()->authenticate();
        $todo = new Todo([
            'title' => $request->title,
            'description' => $request->description,
            'completed' => '0'
        ]);


        if ($this->user->todo()->save($todo))
            return response()->json([
                'success' => true,
                'todo' => $todo
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Sorry, todo could not be added'
            ], 500);
    }

    public function show($id)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $todo = $this->user->todo()->find($id);

        if (!$todo) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, todo with id ' . $id . ' cannot be found'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'todo' => $todo
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $this->user = JWTAuth::parseToken()->authenticate();

        //validate incoming request
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'description' => 'required',
            'completed' => 'boolean'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $todo = $this->user->todo()->find($id);

        if (!$todo) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, todo with id ' . $id . ' cannot be found'
            ], 400);
        }

        $updated = $todo->fill($request->all())->save();

        if ($updated)

            return response()->json([
                'success' => true,
                'message' => 'Todo updated successfully'
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Sorry, todo could not be updated'
            ], 500);
    }

    public function destroy($id)
    {
        $this->user = JWTAuth::parseToken()->authenticate();

        $todo = $this->user->todo()->find($id);

        if (!$todo) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, todo with id ' . $id . ' cannot be found'
            ], 400);
        }

        if ($todo->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Todo deleted successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Todo could not be deleted'
            ], 500);
        }
    }
}
