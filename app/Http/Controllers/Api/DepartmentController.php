<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Company;
use Validator;
use Auth;

class DepartmentController extends BaseController
{
    public function index()
    {
		$company = Company::where('user_id',Auth::id())->first();
        $data = Department::with('employee')->where('company_id',$company->id)->get();
        return $this->sendResponse($data, 'Department List');

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_name' => 'required|string',
            'department_type' => 'required|string',
            'lead_full_name' => 'required|string',
            'lead_email_address' => 'required|email',
            'lead_contact_number' => 'required|string',
            'number_of_employees_in_depart' => 'required|integer',
        ]);

        if($validator->fails())
        {
		    return $this->sendError($validator->errors()->first());
        }
		
        $data = $validator->validated();
		$company = Company::where('user_id',Auth::id())->first();
        $data['company_id'] = $company->id;
		
		if($request->hasFile('image'))
        {
			$imagePath = $request->file('image')->store('resumes/images', 'public');
            $data['image'] = '/storage/'.$imagePath;
        }

        $resume = Department::create($data);

        return $this->sendResponse($resume, 'Department Add Successfully');
    }
	
	public function update(Request $request, $id)
	{
		// $validator = Validator::make($request->all(), [
		// 	'department_name' => 'required|string',
		// 	'department_type' => 'required|string',
		// 	'lead_full_name' => 'required|string',
		// 	'lead_email_address' => 'required|email',
		// 	'lead_contact_number' => 'required|string',
		// 	'number_of_employees_in_depart' => 'required|integer',
		// ]);

		// if ($validator->fails()) {
		// 	return $this->sendError($validator->errors()->first());
		// }

		$data = $request->all();
		$company = Company::where('user_id', Auth::id())->first();
		$data['company_id'] = $company->id;

		$department = Department::where('id', $id)->where('company_id', $company->id)->first();

		if (!$department) {
			return $this->sendError('Department not found or unauthorized access');
		}

		if ($request->hasFile('image')) {
			if (!empty($department->image) && file_exists(public_path($department->image))) {
				unlink(public_path($department->image));
			}

			$imagePath = $request->file('image')->store('resumes/images', 'public');
			$data['image'] = '/storage/' . $imagePath;
		}

		$department->update($data);

		return $this->sendResponse($department, 'Department Updated Successfully');
	}

}
