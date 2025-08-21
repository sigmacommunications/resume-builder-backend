<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mail;
use Validator;
use Auth;

class MailController extends BaseController
{
    public function index()
    {
        $mail = Mail::with('template')->where('user_id', Auth::id())->get();
        return $this->sendResponse($mail, 'Mail List');

    }

    public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'template_id' => 'required',
			'type' => 'nullable|string',
			'name' => 'required|string',
			'phone' => 'nullable|string',
			'email' => 'required|email',
			'summary' => 'nullable|string',
			'date' => 'nullable',
			'details' => 'nullable|string',
			'managerName' => 'nullable|string',
			'subject' => 'nullable|string',
			'companyName' => 'nullable|string',
			'companyAddress' => 'nullable|string',
			'website_url' => 'nullable|url',
			'tamplate_title' => 'nullable|string',
			'tamplate_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			'tamplate_description' => 'nullable|string',
		]);

		if ($validator->fails()) {
			return $this->sendError($validator->errors()->first());
		}

		$data = $validator->validated();
		$data['user_id'] = Auth::id();

		// Handle file upload
		if ($request->hasFile('tamplate_image')) {
			$file = $request->file('tamplate_image');
			$path = $file->store('templates', 'public'); // stores in storage/app/public/templates
			$data['tamplate_image'] = asset('storage/' . $path); // full URL
		}

		$mail = Mail::create($data);

		return $this->sendResponse($mail, 'Mail added successfully.');
	}

    public function show(Mail $mail)
    {
        $this->authorize('view', $mail);
        return $mail;
    }

    public function update(Request $request, Mail $mail)
    {
        $validator = Validator::make($request->all(), [
			'template_id' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'date' => 'nullable',
            'managerName' => 'nullable',
            'subject' => 'nullable',
            'summary' => 'nullable',
            'details' => 'nullable',
			'type' => 'nullable',
        ]);

        if($validator->fails())
        {
		    return $this->sendError($validator->errors()->first());
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();

        $mail = $mail->update($data);

        return $this->sendResponse($mail, 'Mail Update Successfully');
    }

    public function destroy(Mail $mail)
    {
        $mail->delete();
        return response()->json(['message' => 'Mail Deleted Successfully']);
    }
}
