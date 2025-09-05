<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController as BaseController;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Review;
use App\Models\Wallet;
use App\Models\Mail;
use App\Models\User;
use App\Models\Reason;
use App\Models\Company;
use App\Models\TemplateAssign;
use App\Models\Notification;
use Image;
use File;
use Auth;
use Validator;
class UserController extends BaseController
{
	public function __construct()
    {
		$stripe = \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }
	
	public function companyinfo()
	{
		$userId = Auth::id();

		// Load company with departments & employees (no mails yet)
		$company = Company::with(['departments','employee'])->where('user_id', $userId)->first();
		$mail = Mail::with('template')->where('user_id', $userId)->get();
		// Add 'save_template' manually to company object
		$company->save_template = [
			'mail' => $mail
		];

		$assignedTemplates = TemplateAssign::with([
			'employee',
			'assignable' => function ($morphTo) {
				$morphTo->morphWith([
					\App\Models\Mail::class => ['template'],
				]);
			}
		])->where('company_id', $userId)->get();
		$company->assign_template = $assignedTemplates;

		return response()->json([
			'message' => 'Company info with save_template',
			'company_detail' => $company
		]);
	}
	
	
	public function employee_detail($id)
	{
		
		$user = Employee::with([
				'templateAssigns.assignable' => function ($morphTo) {
					$morphTo->morphWith([
						\App\Models\Mail::class => ['template'],
						\App\Models\Career::class => ['template'],
						\App\Models\CoverLetter::class => ['template'],
					]);
				}
			,'templateAssigns.responses'])->find($id);
		return response()->json(['message' => 'Employee Detail', 'employee_info' => $user]);
	}
	
	public function employee_list(Request $request,$id)
    {
		//$company = Company::where('user_id',Auth::id())->first();
		$user = Employee::with('department')->where('company_id',$id)->get();
		//$user = User::with('detail')->where('company_id',$id)->get();
		return response()->json(['message' => 'Employee List successfully', 'employee_list' => $user]);
    }
	
	
	
	public function add_company(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'company_name' => 'required',
			'business_type' => 'required',
			'industry' => 'required',
			'company_number' => 'required',
			'business_email_address' => 'required',
			'business_phone_number' => 'required',
			'company_address' => 'required',
			'company_logo' => 'nullable|image',
			'number_of_employees' => 'required|integer',
			'tax_identification_number' => 'nullable',
			'website_url' => 'nullable'
		]);

		if ($validator->fails()) {
			return $this->sendError($validator->errors()->first());
		}

		$userId = Auth::id();

		// Check if company already exists for this user
		$company = Company::where('user_id', $userId)->first();

		$logo = $company?->company_logo; // Keep existing logo if not uploading new one

		// If new logo uploaded
		if ($request->hasFile('company_logo')) {
			$file = $request->file('company_logo');
			$fileName = md5($file->getClientOriginalName() . time()) . '.' . $file->getClientOriginalExtension();
			$file->move('uploads/company/', $fileName);
			$logo = '/uploads/company/' . $fileName;
		}

		$companyData = [
			'company_name' => $request->company_name,
			'business_type' => $request->business_type,
			'industry' => $request->industry,
			'company_number' => $request->company_number,
			'business_email_address' => $request->business_email_address,
			'business_phone_number' => $request->business_phone_number,
			'website_url' => $request->website_url ?? null,
			'company_address' => $request->company_address,
			'company_logo' => $logo,
			'number_of_employees' => $request->number_of_employees,
			'tax_identification_number' => $request->tax_identification_number ?? null,
		];

		if ($company) {
			// Update existing company
			$company->update($companyData);
		} else {
			// Create new company
			$companyData['user_id'] = $userId;
			Company::create($companyData);
		}

		$user = User::with('company_detail', 'company_detail.departments', 'company_detail.employee', 'roles')->find($userId);

		return response()->json([
			'message' => $company ? 'Company updated successfully' : 'Company added successfully',
			'user_info' => $user
		]);
	}

	
	public function add_employee(Request $request)
    {
		$validator = Validator::make($request->all(), [
            //'name' => 'required|string',
			'employee_email' => 'required|email|unique:users,email',
			'password' => 'required|min:6',

			'full_name' => 'required|string',
			//'employee_email' => 'required|email',
			'employee_phone_number' => 'required|string',
			'department_id' => 'required|exists:departments,id',
			'designation' => 'required|string',
			'joining_date' => 'required',
			'salary' => 'required|numeric'
        ]);
        if($validator->fails())
        {
		    return $this->sendError($validator->errors()->first());
        }
		$fileName = null;
        if($request->hasFile('photo'))
        {
            $file = request()->file('photo');
            $fileName = md5($file->getClientOriginalName() . time()) . '.' . $file->getClientOriginalExtension();
            $file->move('uploads/user/profiles/', $fileName);
            $profile = asset('uploads/user/profiles/'.$fileName);
        }

        $input = $request->except(['confirm_password'],$request->all());
        $input['password'] = bcrypt($input['password']);

        $input['photo'] = '/uploads/user/profiles/'.$fileName;//$profile;
		$input['email_verified_at'] = \Carbon\Carbon::now();
        $user = User::create([
			'company_id' => $request->company_id,
			'name' => $request->name,
			'email' => $request->employee_email,
			'password' => bcrypt($request->password)
		]);
		$user->assignRole('user');
		
		
		$employee = Employee::create([
			'user_id' => $user->id,
			'company_id' => $user->company_id,
			'full_name' => $request->full_name,
			'employee_email' => $request->employee_email,
			'employee_phone_number' => $request->employee_phone_number,
			'department_id' => $request->department_id,
			'designation' => $request->designation,
			'joining_date' => $request->joining_date,
			'salary' => $request->salary,
		]);

        // Wallet::create([
        //     'user_id' => $user->id,
        // ]);


		return response()->json(['message' => 'Employee added successfully', 'employee' => $employee]);
    }

	public function update_employee(Request $request, $id)
	{
		$employee = Employee::find($id);

		if (!$employee) {
			return $this->sendError('Employee not found.');
		}

		$user = $employee->user;

		$validator = Validator::make($request->all(), [
			'employee_email' => 'required|email|unique:users,email,' . $user->id,
			'password' => 'nullable|min:6',
			'full_name' => 'required|string',
			'employee_phone_number' => 'required|string',
			'department_id' => 'required|exists:departments,id',
			'designation' => 'required|string',
			'joining_date' => 'required',
			'salary' => 'required|numeric'
		]);

		if ($validator->fails()) {
			return $this->sendError($validator->errors()->first());
		}

		// Photo upload
		$fileName = $employee->photo; // agar nayi photo na aye to purani photo rahe
		if ($request->hasFile('photo')) {
			$file = $request->file('photo');
			$fileName = md5($file->getClientOriginalName() . time()) . '.' . $file->getClientOriginalExtension();
			$file->move('uploads/user/profiles/', $fileName);
			$fileName = '/uploads/user/profiles/' . $fileName;
		}

		// User update
		$user->update([
			'company_id' => $request->company_id ?? $user->company_id,
			'name' => $request->name ?? $user->name,
			'email' => $request->employee_email,
			'password' => $request->password ? bcrypt($request->password) : $user->password,
		]);

		// Employee update
		$employee->update([
			'full_name' => $request->full_name,
			'employee_email' => $request->employee_email,
			'employee_phone_number' => $request->employee_phone_number,
			'department_id' => $request->department_id,
			'designation' => $request->designation,
			'joining_date' => $request->joining_date,
			'salary' => $request->salary,
			'photo' => $fileName,
		]);

		return response()->json([
			'message' => 'Employee updated successfully',
			'employee' => $employee
		]);
	}



	public function delete_employee($id)
	{
		$company = Company::where('user_id', Auth::id())->first();

		$employee = Employee::where('id', $id)
			->where('company_id', $company->id)
			->first();

		if (!$employee) {
			return $this->sendError('Employee not found or unauthorized access');
		}

		if (!empty($employee->photo) && file_exists(public_path($employee->photo))) {
			unlink(public_path($employee->photo));
		}

		$user = User::find($employee->user_id);
		if ($user) {
			$user->delete();
		}

		$employee->delete();

		return $this->sendResponse([], 'Employee Deleted Successfully');
	}

	
	

    public function reason_list(Request $request)
	{
		try
		{
			$reasons = Reason::get();
			return response()->json(['success'=>true,'message'=>'Reason Lists','reson_list'=>$reasons]);

		}
		catch(\Exception $e)
		{
			return $this->sendError($e->getMessage());
		}
	}

	public function un_reead_notification()
	{
		$notification = Auth::user()->unreadNotifications;
		$notificationold = Auth::user()->readNotifications;
		$unread = count(Auth::user()->unreadNotifications);
		$read = count(Auth::user()->readNotifications);
		// return $notification[0]->data['title'];
		$data = null;
		if($notification)
		{
			foreach($notification as $row)
			{
				$data[] = [
					'id' => $row->id,
					'title' => $row->data,
					// 'description' => $row->data['description'],
					// 'created_at' => $row->data['time'],
					'status' => 'unread'
				];
				// $data[] = $row->data;
			}
		}

		$olddata = null;
		if($notificationold){

			foreach($notificationold as $row)
			{
				$data[] = [
					'id' => $row->id,
					'title' => $row->data['title'],
					'description' => $row->data['description'],
					'read_at' => $row->data['time'],
					'status' => 'read'
				];
			}
		}
		return response()->json(['success'=>true,'unread'=> $unread,'read'=> $read,'notification' => $data]);
	}

	public function wallet()
	{
		try
		{
			$wallet = Wallet::where('user_id',Auth::user()->id)->first();
			return response()->json(['success'=>true,'message'=> 'My Wallet','wallet' => $wallet],200);
		}
		catch(\Eception $e)
		{
			return response()->json(['error'=>$e->getMessage()]);
	   	}
	}

	public function read_notification(Request $request)
	{
		try{
			$validator = Validator::make($request->all(),[
				'notification_id' => 'required',
			]);
			if($validator->fails())
			{

				return response()->json(['success'=>false,'message'=> $validator->errors()->first()]);
			}

			$notification= Notification::find($request->notification_id);
			if($notification){
				$notification->read_at = date(now());
				$notification->save();
				$status= $notification;
				if($status)
				{
					return response()->json(['success'=>true,'message'=> 'Notification successfully deleted']);
				}
				else
				{
					return response()->json(['success'=>false,'message'=> 'Error please try again']);
				}
			}
			else
			{
				return response()->json(['success'=>false,'message'=> 'Notification not found']);
			}
		}
		catch(\Eception $e)
		{
			return response()->json(['error'=>$e->getMessage()]);
	   	}
	}

	public function review(Request $request)
	{
		try
		{
			//return Auth::user()->role;
			$validator = Validator::make($request->all(),[
				'ride_id' =>'required|exists:rides,id',
				'rating' =>'required',
				'text' =>'string',
			]);
			if($validator->fails())
			{
				return $this->sendError($validator->errors()->first(),500);
			}

			//return $assign_user_id;
			$review = Review::create([
				'ride_id' => $request->ride_id,
				'user_id' => Auth::user()->id,
				'rating' => $request->rating,
				'text' => $request->text,
			]);

			return response()->json(['success'=>true,'message'=>'Review Created Successfully','review'=>$review]);

		}
		catch(\Exception $e)
		{
			return $this->sendError($e->getMessage());
		}
	}

    public function profile(Request $request)
	{
		try {
			$user = Auth::user();
			$employee = Employee::where('user_id',$user->id)->first(); 

			$validator = Validator::make($request->all(), [
				'photo' => 'nullable|image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
				'phone' => 'nullable|string',
				'skills' => 'nullable|array',
				'address' => 'nullable|string',
				'date_of_birth' => 'nullable',
			]);

			if ($validator->fails()) {
				return $this->sendError($validator->errors()->first());
			}

			$profilePath = $employee->photo;

			if ($request->hasFile('photo')) {
				$file = $request->file('photo');
				$fileName = md5($file->getClientOriginalName() . time()) . ".photo." . $file->getClientOriginalExtension();
				$file->move('uploads/user/profiles/', $fileName);
				$profilePath = 'uploads/user/profiles/' . $fileName;
			}

			$employee->photo = $profilePath;
			$employee->employee_phone_number = $request->phone ?? $employee->employee_phone_number;
			$employee->full_name = $request->full_name ?? $employee->full_name;
			$employee->skills = $request->skills ? json_encode($request->skills) : $employee->skills;
			$employee->address = $request->address ?? $user->address;
			$employee->date_of_birth = $request->date_of_birth ?? $employee->date_of_birth;

			$employee->save();
			
            $user->load('employee_detail','employee_detail.department','employee_detail.company');

			return response()->json([
				'success' => true,
				'message' => 'Profile updated successfully',
				'user_info' => $user,
			]);
		} catch (\Exception $e) {
			return $this->sendError($e->getMessage());
		}
	}

	public function current_plan(Request $request)
	{
		try{
		//$user= User::findOrFail(Auth::id());
		$user = User::with(['child','goal','temporary_wallet','wallet','payments'])->where('id',Auth::user()->id)->first();

		$amount = 100;
		$charge = \Stripe\Charge::create([
			'amount' => $amount,
			'currency' => 'usd',
			'customer' => $user->stripe_id,
		]);
		if($request->current_plan == 'basic')
		{
			$user->update(['current_plan' =>"premium",'card_change_limit'=>'1','created_plan'=> \Carbon\Carbon::now()]);
			return response()->json(['success'=>true,'message'=>'Current Plan Updated Successfully','user_info'=>$user,'payment' => $charge]);

		}
		elseif($request->current_plan == 'premium')
		{
			$user->update(['current_plan' =>"basic",'card_change_limit'=>'0','created_plan'=> \Carbon\Carbon::now()]);

		 return response()->json(['success'=>true,'message'=>'Current Plan Updated Successfully','user_info'=>$user]);
		}
		else
		{
			return $this->sendError("Invalid Body ");
		}
		}
		catch(\Exception $e){
	  return $this->sendError($e->getMessage());

		}

	}


}
