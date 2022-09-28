<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return view('dashboard.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $parents = Category::all();
        return view('dashboard.categories.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->merge([
            'slug' =>  Str::slug($request->post('name'))
        ]);

        $category = Category::create($request->all());

        return Redirect::route('dashboard.categories.index')
            ->with('success', 'Category created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $category =  Category::findOrFail($id);
        } catch (Exception $e) {

            return redirect()->route('dashboard.categories.index')
                ->with('info', 'Record not found!');
        }

        // SELECT * FROM categoires WHERE id <> id
        // AND (parent_id IS NULL OR  parent_id <> $id)

        $parents = Category::where('id', '<>', $id)

        ->where(function($query) use($id) {

          $query->whereNull('parent_id')
                ->orwhere('parent_id', '<>', $id);
        })
           ->get();   

        return view('dashboard.categories.edit', compact('category', 'parents'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $category = Category::findOrFail($id);

        $category->update($request->all());

        return Redirect::route('dashboard.categories.index')
            ->with('success', 'Category updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*  $category = Category::findOrFail($id);
        $category->delete($id); */

        // Category::where('id','=','id')->delete();

        Category::destroy($id);

        return Redirect::route('dashboard.categories.index')
            ->with('success', 'Category deleted');
    }
}
