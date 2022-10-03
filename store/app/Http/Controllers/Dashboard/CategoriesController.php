<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Dimensions;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = request();
        $query = Category::query();

        if($name = $request->query('name')){
            $query->where('name','LIKE', "%{$name}%");
        }
        if($status = $request->query('status')){
            $query->where('status','=', $status);
        }

        $categories = $query->paginate(2); //return collection object
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
        $category = new Category();
        return view('dashboard.categories.create', compact('parents', 'category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $clean_data =  $request->validate(Category::rules(), [
            'name.required' => 'This field (:attribute) is required',
            'name.unique' => 'This name is already exists!'
        ]);

        $request->merge([
            'slug' =>  Str::slug($request->post('name'))
        ]);
        $data = $request->except('image');
        $data['image'] = $this->uploadImage($request);
        //Mass assignment
        $category = Category::create($data);

        //PRG
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

        // SELECT * FROM categories WHERE id <> id
        // AND (parent_id IS NULL OR  parent_id <> $id)

        $parents = Category::where('id', '!=', $id)

            ->where(function ($query) use ($id) {

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
    public function update(CategoryRequest $request, $id)
    {
        //  انا هنا بلزمنيش اعمل جملة الفالديشن لاني استخدمت الفالديشن الي عملته **مش زي الستور بختلف وهنا الفرق

        //  $request->validate(Category::rules($id));

        $category = Category::findOrFail($id);

        $old_image = $category->image;

        $data = $request->except('image');
        $new_image = $this->uploadImage($request);
        if ($new_image) {
            $data['image'] = $new_image;
        }

        $category->update($data);

        if ($old_image && $new_image) {
            Storage::disk('public')->delete($old_image);
        }

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
        $category = Category::findOrFail($id);
        $category->delete();

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        // Category::where('id','=','id')->delete();

        //   Category::destroy($id);
        //   هاي ما بتزبط لو بدي احذف قسم معين ويحذفلي الصورة معاه عشان ما بتعطيني وين الصورة موجودة

        return Redirect::route('dashboard.categories.index')
            ->with('success', 'Category deleted');
    }

    protected function uploadImage(Request $request)
    {
        if (!$request->hasFile('image')) {
            return;
        }
        $file = $request->file('image');

        $path =  $file->store('uploads', [
            'disk' => 'public'
        ]);

        return $path;
    }
}
