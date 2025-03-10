<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\RideStatusNotification;
use App\Models\Ride;
use App\Models\User;
use App\Events\RideCreated;
use App\Events\Customer;
use App\Events\CityPrice;
use Pusher\Pusher;
use Auth;
use App\Services\FirebaseService;

class BookRideController extends BaseController
{

    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function getbookride($id)
    {
        $ride = Ride::withCount('ride')->with('rider','carinfo','rider.review')->find($id);
        if ($ride && $ride->rider)
        {
            $ride->rider->rating = $ride->rider->review()->avg('rating'); // Assuming 'rating' is the column name in the 'review' table
        }

        return response()->json(['success'=> true, 'message' => 'Ride Info','ride_info'=>$ride]);
    }

    public function rider_arrived($id)
    {
        $ride = Ride::withCount('ride')->with('rider','user','carinfo','rider.review')->find($id);


        // $user = User::find($ride->user_id);

        // $rider = User::find($ride->rider_id); // rider ka user model
        // $ride['title'] = 'Rider Waiting';
        // $ride['body'] = $user->first_name . ' ' . $user->last_name .' Your ride has arrived at your location.';

        // $rider->notify(new RideStatusNotification($ride));


        $user = User::find($ride->user_id); // rider ka user model

        $body = $user->first_name . ' ' . $user->last_name .' Your ride has arrived at your location.';
        $title = request()->text;
        $fcmToken = $user->device_token;
        $response = $this->firebaseService->sendNotification($fcmToken, $title, $body);

        $message = [
            'ride_id' => $ride->id,
            'rider_id' => $ride->rider_id,
            'user_id' => $ride->user_id,
            'text' => 'Rider has arrived at your location',
            'createdAt' => $ride->updated_at,
            'ride_info' => $ride,
        ];

        // Broadcast the event
        broadcast(new Customer((object)$message))->toOthers();

        // $title = 'Rider Waiting';
        // $fcmToken = $user->device_token;
        // $response = $this->firebaseService->sendNotification($fcmToken, $title, $body);

        return response()->json(['success'=> true, 'message' => 'Ride Info','ride_info'=>$ride]);
    }

    public function bookRide(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'car_id'=>'required',
            'location_from'=>'required',
            'location_to'=>'required',
            'distance'=>'required',
            'amount'=>'required',
            'pickup_location_lat'=>'required',
            'pickup_location_lng'=>'required',
            'dropoff_location_lat'=>'required',
            'dropoff_location_lng'=>'required',
        ]);

        if($validator->fails()) {
            return response()->json(['success'=>false,'message'=>$validator->errors()],500);
        }
        // if($cityprice)
        // {
        //     $price = $cityprice->price;
        // }
        // else
        // {
        //     $price = $request->amount;
        // }

        $ride = Ride::create([
            'user_id' => Auth::user()->id,
            'car_id' => $request->car_id,
            'location_from' => $request->location_from,
            'location_to' => $request->location_to,
            'amount' => $request->amount,
            'distance' => $request->distance,
            'pickup_location_lat' => $request->pickup_location_lat,
            'pickup_location_lng' => $request->pickup_location_lng,
            'dropoff_location_lat' => $request->dropoff_location_lat,
            'time' => $request->time,
            // 'stop' => $request->stop,
            'dropoff_location_lng' => $request->dropoff_location_lng,
            'status' => 'pending',
        ]);

        if($request->stop)
        foreach($request->stop as $pickup)
        {   
            RidePickup::create([
                'ride_id' => $ride->id,
                'pickup_lat' => $pickup->pickup_lat,
                'pickup_lng' => $pickup->pickup_lng,
            ]);
        }

        $data = Ride::with('carinfo','rider','pickup')->find($ride->id);
        $data['user_info'] = Auth::user();
        // Send a notification to the user
        $admin = User::where('role','admin')->first(); // Admin ka user model

        $ride['title'] = 'New Ride Request';
        $ride['body'] = $admin->first_name . ' ' . $admin->last_name .' New Ride Request.';

        $admin->notify(new RideStatusNotification($data));




        return $this->sendResponse($ride ,'Ride request sent to admin.',200);

        // return response()->json([
        //     'message' => 'Ride booked successfully!',
        //     'ride' => $ride
        // ], 201);
    }

    protected function sendRideNotification(Ride $ride)
    {
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true
            ]
        );

        $data['message'] = 'Your ride from ' . $ride->location_from . ' to ' . $ride->location_to . ' has pending!';
        $data['data'] = $ride;
        $pusher->trigger('ride-channel', 'ride-booked', $data);
    }
}
