<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Document;
use Validator;
use Auth;

class DocumentController extends BaseController
{
	public function index(){
        try{

            $document = Document::where('user_id', Auth::user()->id)->get();
			
       		$data = [];
            foreach($document as $doc){

            $data[] = [
                'id' => $doc['id'],
                'user_id' => $doc['user_id'],
				'name' => $doc['document_name'],
                'uri' => asset('/').$doc['document'],
				'thumbnail' => asset('/').$doc['thumbnail'],
				'created_at' => $doc['created_at'],   
				 'updated_at' => $doc['updated_at'],  
            ];
				
			
        }
			
            return response()->json(['success'=>true,'message'=>'document List Successfully','Document' => $data]);
        }
        catch(\Eception $e)
        {
                return $this->sendError($e->getMessage());
        } 
    }
	
    public function store(Request $request)
	{
		try {
			$validator = Validator::make($request->all(), [
				// Allow image and document both
				'document' => 'required|mimes:jpeg,png,jpg,bmp,gif,svg,pdf,doc,docx|max:20480', // max 20MB
				'thumbnail' => 'image|mimes:jpeg,png,jpg,bmp,gif,svg|max:5120', // max 5MB
			]);

			if ($validator->fails()) {
				return $this->sendError($validator->errors()->first());
			}

			if ($request->hasFile('document')) {
				$document = $request->file('document');
				$extension = $document->getClientOriginalExtension();
				$fileName = md5($document->getClientOriginalName() . time()) . "_resumebuilder." . $extension;

				$document->move('uploads/storage/document/', $fileName);
				$documentPath = 'uploads/storage/document/' . $fileName;
			}

			if ($request->hasFile('thumbnail')) {
				$thumbnail = $request->file('thumbnail');
				$fileName = md5($thumbnail->getClientOriginalName() . time()) . "_resumebuilder." . $thumbnail->getClientOriginalExtension();

				$thumbnail->move('uploads/storage/thumbnail/', $fileName);
				$thumbnailPath = 'uploads/storage/thumbnail/' . $fileName;
			}

			$storage = new Document();
			$storage->user_id = Auth::id();
			$storage->document_name = $request->name;
			$storage->thumbnail = $thumbnailPath ?? '';
			$storage->document = $documentPath ?? '';
			$storage->save();

			return response()->json(['success' => true, 'message' => 'Document Uploaded Successfully']);

		} catch (\Exception $e) {
			return $this->sendError($e->getMessage());
		}
	}
	
	
	public function destroy($id)
	{
		try {
			$document = Document::find($id);

			if (!$document) {
				return response()->json(['success' => false, 'message' => 'Document not found'], 404);
			}

			// Delete document file if exists
			if ($document->document && file_exists(public_path($document->document))) {
				unlink(public_path($document->document));
			}

			// Delete thumbnail file if exists
			if ($document->thumbnail && file_exists(public_path($document->thumbnail))) {
				unlink(public_path($document->thumbnail));
			}

			// Delete database record
			$document->delete();

			return response()->json(['success' => true, 'message' => 'Document deleted successfully']);
		} catch (\Exception $e) {
			return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}


}
