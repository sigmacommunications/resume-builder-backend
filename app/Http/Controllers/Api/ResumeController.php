<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resume;
use Validator;
use Auth;

class ResumeController extends BaseController
{
    public function index()
    {
        $resume = Resume::where('user_id', Auth::id())->get();
        return $this->sendResponse($resume, 'Resmue List');

    }

    public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'email' => 'required|email',
			'date' => 'nullable',
			'month' => 'nullable',
			'summary' => 'nullable',
			'address' => 'nullable',
			'details' => 'nullable',
			'skills' => 'nullable',
			'phone' => 'nullable',
			'education' => 'nullable',
			'degreeName' => 'nullable',
			'degreePlaceName' => 'nullable',
			'degreeYear' => 'nullable',
			'CertificateName' => 'nullable',
			'CertificatePlaceName' => 'nullable',
			'CertificatYear' => 'nullable',
			'positonName' => 'nullable',
			'experiencePlaceName' => 'nullable',
			'DateofJoining' => 'nullable',
			'DateofEnding' => 'nullable',
			'summaryDetails' => 'nullable',
			'projects' => 'nullable',
			'tamplate_title' => 'nullable',
			'tamplate_image' => 'nullable|image',
			'tamplate_description' => 'nullable',
			'type' => 'nullable',
		]);

		if ($validator->fails()) {
			return $this->sendError($validator->errors()->first());
		}

		$data = $validator->validated();
		$data['user_id'] = Auth::id();

		// Convert skills and projects to JSON if they're arrays
		$data['skills'] = is_array($request->skills) ? json_encode($request->skills) : $request->skills;
		$data['projects'] = is_array($request->projects) ? json_encode($request->projects) : $request->projects;

		// Handle image upload
		if ($request->hasFile('tamplate_image')) {
			$imagePath = $request->file('tamplate_image')->store('resumes/images', 'public');
			$data['tamplate_image'] = '/storage/' . $imagePath;
		}

		$resume = Resume::create($data);

		return $this->sendResponse($resume, 'Resume added successfully');
	}

    public function show(Resume $resume)
    {
        $this->authorize('view', $resume);
        return $resume;
    }

    public function update(Request $request, Resume $resume)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'date' => 'nullable',
            'month' => 'nullable',
            'summary' => 'nullable',
            'address' => 'nullable',
            'details' => 'nullable',
            'skills' => 'nullable',
            'phone' => 'nullable',
            'education' => 'nullable',
            'degreeName' => 'nullable',
            'degreePlaceName' => 'nullable',
            'degreeYear' => 'nullable',
            'CertificateName' => 'nullable',
            'CertificatePlaceName' => 'nullable',
            'CertificatYear' => 'nullable',
            'positonName' => 'nullable',
            'experiencePlaceName' => 'nullable',
            'DateofJoining' => 'nullable',
            'DateofEnding' => 'nullable',
            'summaryDetails' => 'nullable',
			'type' => 'nullable',
        ]);

        if($validator->fails())
        {
		    return $this->sendError($validator->errors()->first());
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();
		if($request->hasFile('image'))
        {
			$imagePath = $request->file('image')->store('resumes/images', 'public');
            $data['image'] = '/storage/'.$imagePath;
        }
        $resume = $resume->update($data);

        return $this->sendResponse($resume, 'Resmue Update Successfully');
    }

    public function destroy(Resume $resume)
    {
        $resume->delete();
		return $this->sendResponse('Resmue Delete Successfully');
    }
	
	
	public function deleteAllResumes()
	{
		// Get all resumes
		$resumes = Resume::all();

    // Loop through all resumes and delete their images
		foreach ($resumes as $resume) {
			if ($resume->image) {
				// Check if the file exists before trying to delete it
				$imagePath = $resume->image;

				// Remove 'public/' from the image path before passing to delete
				if (Storage::disk('public')->exists($imagePath)) {
					// Delete the image from storage
					Storage::disk('public')->delete($imagePath);
				}
			}

			// Delete the resume from the database
			$resume->delete();
		}
		return response()->json(['message' => 'All resumes and images have been deleted successfully.'], 200);
	}
}
