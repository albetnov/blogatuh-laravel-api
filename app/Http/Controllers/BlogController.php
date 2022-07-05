<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blogs = Blog::orderBy('id', 'DESC')->get();
        $response = [
            'message' => 'BlogAtuh | List of blogs',
            'data' => $blogs
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    private function checkForCategory($request)
    {
        // Check whenever the category exists 
        $categories = $request->categories;
        $data = explode(',', $categories);
        $source = Category::whereIn('id', $data)->get();
        if (count($data) != count($source->pluck('id')->toArray())) {
            return false;
        }
        return true;
    }

    private function errorHandler()
    {
        return response()->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'categories' => ['required'],
            'content' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!$this->checkForCategory($request)) {
            return $this->errorHandler();
        };

        try {
            $blog = Blog::create($request->all());
            $response = [
                'message' => 'Blog created successfully',
                'data' => $blog
            ];
            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Failed ' . $e->errorInfo
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(Blog $blog)
    {
        $response = [
            'message' => "{$blog->name} Detail",
            'data' => $blog
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blog $blog)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'categories' => ['required'],
            'content' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!$this->checkForCategory($request)) {
            return $this->errorHandler();
        };

        try {
            $blog->update($request->all());
            $response = [
                'message' => 'Blog updated successfully',
                'data' => $blog
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Blog update failed ' . $e->errorInfo,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blog $blog)
    {
        try {
            $blog->delete();
            $response = [
                'message' => 'Blog deleted successfully'
            ];
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Blog delete failed ' . $e->errorInfo
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
