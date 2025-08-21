<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Survey;
use Validator;
use Auth;

class SurveyController extends BaseController
{
	
	// GET API - List Surveys
    public function index()
    {
		$resume = Survey::get();
        return $this->sendResponse($resume, 'Survey List');
    }
	
    public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'question' => 'required|array',
			'options' => 'required|array',
			'type' => 'nullable|string',
			'company_name' => 'nullable|string',
			//'company_logo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
			'website_url' => 'nullable|url',
			'tamplate_title' => 'nullable|string',
			//'tamplate_image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
			'tamplate_description' => 'nullable|string',
		]);

		if ($validator->fails()) {
			return $this->sendError($validator->errors()->first());
		}

		// Handle file uploads
		$companyLogoPath = null;
		$tamplateImagePath = null;

		if ($request->hasFile('company_logo')) {
			$companyLogoPath = $request->file('company_logo')->store('uploads/company_logos', 'public');
		}

		if ($request->hasFile('tamplate_image')) {
			$tamplateImagePath = $request->file('tamplate_image')->store('uploads/tamplate_images', 'public');
		}

		$survey = Survey::create([
			'user_id' => Auth::id(),
			'question' => $request->question,
			'options' => $request->options,
			'type' => $request->type,
			'company_name' => $request->company_name,
			'company_logo' => $companyLogoPath ? asset('storage/' . $companyLogoPath) : null,
			'website_url' => $request->website_url,
			'tamplate_title' => $request->tamplate_title,
			'tamplate_image' => $tamplateImagePath ? asset('storage/' . $tamplateImagePath) : null,
			'tamplate_description' => $request->tamplate_description,
		]);

		return response()->json([
			'message' => 'Survey created successfully!',
			'data' => $survey,
		], 200);
	}
}
