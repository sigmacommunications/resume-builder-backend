<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\TemplateAssign;

class TemplateAssignController extends BaseController
{
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
