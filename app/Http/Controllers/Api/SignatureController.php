<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Signature;
use Auth;

class SignatureController extends BaseController
{
	public function index(){
        try{

            $signature = Signature::where('user_id',Auth::user()->id)->get();
       	//	 $data =[];
         //   foreach($signature as $sign){

          //  $data[] = [
          //      'id' => $sign['id'],
          //      'user_id' => $sign['user_id'],
          //      'uri' => $sign['signature'],   
          //  ];    
        //}
//            return response()->json(['success'=>true,'message'=>'document List Successfully','Signature' => $data]);
			return response()->json(['success'=>true,'message'=>'document List Successfully','Signature' => $signature]);
        }
        catch(\Eception $e)
        {
                return $this->sendError($e->getMessage());
        } 
    }
	
    public function store(Request $request)
	{
        try{

            $validator = Validator::make($request->all(),[
                'signature' =>'nullable',
            ]);
            
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            }

            $storage = new Signature();
            $storage->user_id = $request->user_id;
            $storage->uri = $request->uri;
			$storage->name = $request->name;
			$storage->date = $request->date;
            $storage->save();

            return response()->json(['success'=>true,'message'=>'Signature Updated Successfully']);
        }
        catch(\Eception $e)
        {
                return $this->sendError($e->getMessage());
        }   
    }
	
	public function destroy($id)
	{
		try {
			$signature = Signature::find($id);

			if (!$signature) {
				return response()->json(['success' => false, 'message' => 'Signature not found'], 404);
			}

			$signature->delete();

			return response()->json(['success' => true, 'message' => 'Signature deleted successfully']);
		} catch (\Exception $e) {
			return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

}
