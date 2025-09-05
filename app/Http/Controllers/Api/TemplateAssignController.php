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
            'employee_id' => 'required|exists:employees,id',
            'template_assign_id' => 'required|exists:template_assigns,id',
            'response_type' => 'required|in:text,image,document,signature',
            'response_value' => 'required'
        ]);

        if ($validator->fails()) {
			return $this->sendError($validator->errors()->first());
		}
        $savedResponses = [];

        // Agar image/document hua to multiple allow hoga
        if (in_array($request->response_type, ['image', 'document','signature'])) {
            foreach ($request->file('response_value') as $file) {
                $path = $file->store('responses', 'public');

                $response = \App\Models\TemplateResponse::create([
                    'employee_id' => $request->employee_id,
                    'template_assign_id' => $request->template_assign_id,
                    'response_type' => $request->response_type,
                    'response_value' => $path,
                ]);

                $savedResponses[] = $response;
            }
        } else {
            // text ya signature single hoga
            $response = \App\Models\TemplateResponse::create([
                'employee_id' => $request->employee_id,
                'template_assign_id' => $request->template_assign_id,
                'response_type' => $request->response_type,
                'response_value' => $request->response_value,
            ]);

            $savedResponses[] = $response;
        }


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
