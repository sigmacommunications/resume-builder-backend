<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use App\Models\Goal;
use App\Models\Child;
use App\Models\Review;
use App\Models\Company;
use App\Models\TemporaryWallet;
use App\Models\Tranasaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Mail\SendVerifyCode;
use Mail;
use Carbon\Carbon;
use Twilio\Rest\Client;
use Hash;
use Image;
use File;
use Stripe\Customer;
use DB;

class RegisterController extends BaseController
{
    public function userinfo($email)
    {
        return User::where('email', $email)->first();
    }

    public function register(Request $request)
    {
		$validator = Validator::make($request->all(), [
            'role' => 'required',
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
           // 'phone' => 'required',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
		//	'photo' => 'image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
        ]);
        if($validator->fails())
        {
		    return $this->sendError($validator->errors()->first());
        }
		//$fileName = null;
        //if($request->hasFile('photo'))
        //{
        //    $file = request()->file('photo');
        //    $fileName = md5($file->getClientOriginalName() . time()) . '.' . $file->getClientOriginalExtension();
        //    $file->move('uploads/user/profiles/', $fileName);
        //    $profile = asset('uploads/user/profiles/'.$fileName);
        //}

        //$input = $request->except(['confirm_password','role'],$request->all());
        //$input['password'] = bcrypt($input['password']);

        //$photo = '/uploads/user/profiles/'.$fileName;//$profile;
		$input['email_verified_at'] = Carbon::now();
        $user = User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => bcrypt($request->password),
			//'photo' => $photo,
			'phone' => $request->phone,
		]);
		$user->assignRole($request->role);
		
		if($request->role == 'company')
		{
			$user = User::with('company','roles')->where('email',$request->email)->first();
		}
		else
		{
			$user = User::with('roles')->where('email',$request->email)->first();
		}
        // Wallet::create([
        //     'user_id' => $user->id,
        // ]);

        $token =  $user->createToken('resume-builder')->plainTextToken;
        $users = $this->userinfo($request->email);

		return response()->json(['success'=>true,'message'=>'Register successfully','token'=>$token,'user_info'=>$user]);
    }


    public function login(Request $request)
	{
		if (!empty($request->email) || !empty($request->password)) {
			$validator = Validator::make($request->all(), [
				'email' => 'required|email|exists:users,email',
				'password' => 'required',
			]);

			if ($validator->fails()) {
				return $this->sendError($validator->errors()->first());
			}

			if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
				$user = Auth::user();
				$user->device_token = $request->device_token;
				$user->save();

				$token = $user->createToken('app_api')->plainTextToken;

				// Load common relationships
				$user->load('roles');

				// Check role and load role-specific relationships
				if ($user->hasRole('company')) {
					$user->load('company_detail', 'company_detail.departments', 'company_detail.employee');
				} elseif ($user->hasRole('user')) {
					$user->load('employee_detail','employee_detail.department','employee_detail.company');
				}

				return response()->json([
					'success' => true,
					'message' => 'User Logged In successfully',
					'token' => $token,
					'user_info' => $user
				]);
			} else {
				return $this->sendError('Incorrect Password');
			}
		} else {
			return $this->sendError('Email & Password are Required');
		}
	}


    //public function me()
    //{
    //    //$user = User::with(['company','company.departments','company.employee'])->where('id',Auth::user()->id)->first();
	//	$user = Company::with(['departments','employee'])->where('user_id',Auth::id())->first();
    //    return response()->json(['success'=>true,'message'=>'User Fetch successfully','company_info'=>$user]);
    //}

    public function logout()
    {
        if(Auth::check())
        {
            $user = Auth::user()->token();
            $user->revoke();
            $success['success'] =true;
            return $this->sendResponse($success, 'User Logout successfully.');
        }
        else
        {
            return $this->sendError('No user in Session .');
        }
    }

    public function user(Request $request)
    {
        if(Auth::check())
        {
            $success['user_info'] = $request->user();
            return $this->sendResponse($success, 'Current user successfully.');
        }
        else
        {
            return $this->sendError('No user in Session .');
        }
    }

    public function verify(Request $request)
    {
		$validator = Validator::make($request->all(),['email_code'=>'required']);
        if($validator->fails())
        {
            return $this->sendError($validator->errors()->first());
        }

        $user = User::firstWhere('email_code',$request->email_code);
        if($user == null)
        {
            return $this->sendError('Token Expire or Invalid');
        }
        else
        {
            $user->update(['email_verified_at'=>Carbon::now(),'email_code'=>null]);
            $success['success'] =true;
            return $this->sendResponse($success, 'Email verified Successfully');
        }
    }

    public function change_password(Request $request)
    {
        try
        {

            $validator = Validator::make($request->all(),[
                'current_password' => 'required',
                'new_password' => 'required|same:confirm_password|min:8',
                'confirm_password' => 'required',
            ]);

            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            }
            $user = Auth::user();

            if (!Hash::check($request->current_password,$user->password))
            {
                return $this->sendError('Current Password Not Matched');
            }
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json(['success'=>true,'message'=>'Password Successfully Changed','user_info'=>$user]);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        }
    }

    public function noauth()
    {
	    return $this->sendError('session destroyed , Login to continue!');
	}
}
