<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Image;
use Auth;

class ImageController extends BaseController
{
	public function index(){
        try{

			$image = Image::where('user_id',Auth::user()->id)->get();
          	$data =[];
            foreach($image as $img){
			
            $data[] = [
                'id' => $img['id'],
                'user_id' => $img['user_id'],
                'document_name' => $img['document_name'],   
				'uri' => asset('/').$img['image'],   
				 'created_at' => $img['created_at'],   
				 'updated_at' => $img['updated_at'],   
            ];    
        }
            return response()->json(['success'=>true,'message'=>'Image List Successfully','image' => $data]);
        }
        catch(\Eception $e)
        {
                return $this->sendError($e->getMessage());
        } 
    }
	
	public function store(Request $request)
	{
		try
		{
			$validator = Validator::make($request->all(),[
				 'image' => 'image|mimes:jpeg,png,jpg,bmp,gif,svg',
			 ]);

			 if($validator->fails())
			 {
				 return $this->sendError($validator->errors()->first());
			 }

			 if($request->hasFile('image')) 
			 {
				 $image = request()->file('image');
				 $fileName = md5($image->getClientOriginalName() . time()) . "resume-builder." . $image->getClientOriginalExtension();
				 $image->move('uploads/storage/image/', $fileName);  
				 $profile = 'uploads/storage/image/'.$fileName;
			 }

			 $storage = new Image();
			 $storage->user_id = $request->user_id;
			 $storage->image = isset($profile) ? $profile : '';
			 $storage->save();

			 return response()->json(['success'=>true,'message'=>'Image Updated Successfully']);
         }
         catch(\Eception $e)
         {
                 return $this->sendError($e->getMessage());
         }   
	}
	
    public function destroy($id)
	{
		try {
			$image = Image::find($id);

			if (!$image) {
				return response()->json(['success' => false, 'message' => 'Image not found'], 404);
			}

			// Delete image file from storage if it exists
			if ($image->image && file_exists(public_path($image->image))) {
				unlink(public_path($image->image));
			}

			// Delete the database record
			$image->delete();

			return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
		} catch (\Exception $e) {
			return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}
	
	public function ImageDelete($id)
    {
        try
        {
			Image::where('id',$id)->delete();
			return response()->json(['success'=>true,'message'=>'image Delete Successfully']);
        }
        catch(\Eception $e)
        {
                return $this->sendError($e->getMessage());
        }
    }
}
