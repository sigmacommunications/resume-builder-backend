<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $category=Category::paginate(10);
        return view('backend.category.index')->with('categories',$category);
    }

    public function create()
    {
        // $parent_cats=Category::orderBy('title','ASC')->get();
        return view('backend.category.create');
    }

    public function store(Request $request)
    {
        // return $request->all();
        $this->validate($request,[
            'name'=>'string|required',
            'status'=>'required|in:active,inactive',
        ]);
        $data= $request->all();
        $slug=Str::slug($request->name);
        $count=Category::where('slug',$slug)->count();
        if($count>0){
            $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
        }
        $data['slug']=$slug;

        // return $data;   
        $status=Category::create($data);
        if($status){
            request()->session()->flash('success','Category successfully added');
        }
        else{
            request()->session()->flash('error','Error occurred, Please try again!');
        }
        return redirect()->route('category.index');


    }

    public function edit($id)
    {
        $category=Category::findOrFail($id);
        return view('admin.category.edit')->with('category',$category);
    }

    
    public function update(Request $request, $id)
    {
        // return $request->all();
        $category=Category::findOrFail($id);
        $this->validate($request,[
            'title'=>'string|required',
            'summary'=>'string|nullable',
            'status'=>'required|in:active,inactive',
        ]);
        $data= $request->all();
        if($request->file('photo'))
        {
            \File::delete($category->photo);

            $file1 = request()->file('photo');
            $pf = public_path('uploads/category/');
            $fileName1 = md5($file1->getClientOriginalName()) . time() . "." . $file1->getClientOriginalExtension();
            $file1->move($pf,$fileName1);
            $path = asset('/uploads/category/'.$fileName1);
            $data['photo'] = $path;
        }
        $status=$category->fill($data)->save();
        if($status){
            request()->session()->flash('success','Category successfully updated');
        }
        else{
            request()->session()->flash('error','Error occurred, Please try again!');
        }
        return redirect()->route('category.index');
    }

    
    public function destroy($id)
    {
        $category=Category::findOrFail($id);
        // return $child_cat_id;
        \File::delete($category->photo);

        $status=$category->delete();
        
        if($status){
            request()->session()->flash('success','Category successfully deleted');
        }
        else{
            request()->session()->flash('error','Error while deleting category');
        }
        return redirect()->route('category.index');
    }

}
