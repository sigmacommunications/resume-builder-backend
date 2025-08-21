<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Category;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::latest()->paginate(10);
        return view('backend.template.index', compact('templates'));
    }
    
    public function create()
    {
        $data['categories'] = Category::get();
        return view('backend.template.create',$data);
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'heading' => 'required|string|max:255',
        //     'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        //     'type' => 'required|string|max:100',
        //     'key' => 'required',
        //     'description' => 'nullable|string',
        // ]);

        
        // $data = $request->except('image');
        $data = $request->all();
        // if ($request->hasFile('image')) {
        //     $imageName = time().'.'.$request->image->extension();  
        //     $path = $request->image->storeAs('categories', $imageName, 'public');
        //     $data['image'] = $path;
        // }

        Template::create($data);

        return redirect()->route('template.index')
                        ->with('success', 'Template created successfully.');
    }

    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'heading' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|string|max:100',
            'key' => 'required|string|max:100|unique:categories,key,'.$category->id,
            'description' => 'nullable|string',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            
            $imageName = time().'.'.$request->image->extension();  
            $path = $request->image->storeAs('categories', $imageName, 'public');
            $data['image'] = $path;
        }

        $category->update($data);

        return redirect()->route('categories.index')
                        ->with('success', 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        // Delete image if exists
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('categories.index')
                        ->with('success', 'Category deleted successfully');
    }
}
