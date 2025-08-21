<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CareerBlog;
use Validator;
use Auth;
use Illuminate\Support\Facades\Storage;

class CareerBlogController extends BaseController
{
    // GET API - List Surveys
    public function index()
    {
		$resume = CareerBlog::get();
        return $this->sendResponse($resume, 'Blog Lists');
    }
	
    public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'company_name' => 'required|string|max:255',
			'designation' => 'required|string|max:255',
			'description' => 'required|string',
			'heading' => 'required|string|max:255',
			'name' => 'required|string|max:255',
			'website_url' => 'nullable|url|max:255',
			'tamplate_title' => 'nullable|string|max:255',
			'tamplate_description' => 'nullable|string',
		]);

		if ($validator->fails()) {
			return $this->sendError($validator->errors()->first());
		}

		$input = $request->all();

		if ($request->hasFile('logo')) {
			$path = $request->file('logo')->store('career-blogs/logo', 'public');
			$input['logo'] = 'storage/' . $path;
		}

		if ($request->hasFile('image')) {
			$path = $request->file('image')->store('career-blogs/image', 'public');
			$input['image'] = 'storage/' . $path;
		}

		if ($request->hasFile('tamplate_image')) {
			$path = $request->file('tamplate_image')->store('career-blogs/tamplate_image', 'public');
			$input['tamplate_image'] = 'storage/' . $path;
		}

		$input['user_id'] = Auth::id();

		$blog = CareerBlog::create($input);

		return response()->json([
			'message' => 'Blog added successfully!',
			'data' => $blog,
		], 200);
	}
}
