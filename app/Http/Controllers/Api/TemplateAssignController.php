<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\TemplateAssign;

class TemplateAssignController extends BaseController
{


    public function storeResponse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_assign_id' => 'required|exists:template_assigns,id',
            'response_type' => 'required|in:text,image,document,signature',
            'response_value' => 'required'
        ]);

        if ($validator->fails()) {
			return $this->sendError($validator->errors()->first());
		}

        $response = new TemplateResponse();
        $response->employee_id = auth()->id(); // ya employee ka id jo pass kro
        $response->template_assign_id = $request->template_assign_id;
        $response->response_type = $request->response_type;

        // File upload handling
        if (in_array($request->response_type, ['image', 'document'])) {
            $path = $request->file('response_value')->store('responses', 'public');
            $response->response_value = $path;
        } else {
            $response->response_value = $request->response_value;
        }

        $response->save();

        return response()->json([
            'success' => true,
            'message' => 'Response saved successfully',
            'data' => $response
        ]);
    }

    // Assign template to employee
    public function assign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|array',
        	'employee_id.*' => 'exists:employees,id',
            'assignable_id' => 'required|integer',
            'assignable_type' => 'required|string|in:mail,career,cover_letter',
        ]);
		
		if ($validator->fails()) {
			return $this->sendError($validator->errors()->first());
		}

        $modelMap = [
            'mail' => \App\Models\Mail::class,
            'career' => \App\Models\Career::class,
            'cover_letter' => \App\Models\CoverLetter::class,
        ];

        $assignableClass = $modelMap[$request->assignable_type];

        foreach ($request->employee_id as $employeeId) {
			TemplateAssign::create([
				'employee_id' => $employeeId,
				'assignable_id' => $request->assignable_id,
				'assignable_type' => $assignableClass,
				'type' => $request->assignable_type
			]);
		}
        return response()->json(['success' => true, 'message' => 'Template assigned successfully']);
    }
}
