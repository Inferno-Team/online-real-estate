<?php

namespace App\Http\Controllers;

use App\Models\RealEstate;
use App\Models\RealEstateRoomImages;
use App\Models\RealEstateRooms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RealEstateController extends Controller
{
    public function recentRealEstate(Request $request)
    {
        $estates = RealEstate::orderBy('created_at', 'desc')
            ->with('owner')->get();
        return response()->json($estates, 200);
    }
    public function addRealEstate(Request $request)
    {
        // request [long , lat, type,location,budget,buy_type,direction,area]
        $user = Auth::user();
        if ($user->type == 'Owner') {
            $estate = RealEstate::create([
                'long' => $request->long,
                'lat' => $request->lat,
                'type' => $request->type,
                'user_id' => $user->id,
                'location' => $request->location,
                'rate' => 0,
                'buy_type' => $request->buy_type,
                'budget' => $request->budget,
                'img_url' => '',
                'img360_url' => '',
                'area' => $request->area,
                'direction' => $request->direction,

            ]);
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $file_extention = $image->getClientOriginalExtension();
                $file_name = time() . '.' . $file_extention;  // 546165165.jpg
                $path = '/public/images/real_estates';
                $image->storeAs($path, $file_name);
                $estate->img_url = '/storage/images/real_estates/' . $file_name;
                $estate->save();
            }
            if ($request->hasFile('image360')) {
                $image = $request->file('image360');
                $file_extention = $image->getClientOriginalExtension();
                $file_name = time() . '.' . $file_extention;  // 546165165.jpg
                $path = '/public/images/360';
                $image->storeAs($path, $file_name);

                $estate->img360_url = '/storage/images/360/' . $file_name;
                $estate->save();
            }
            return response()->json([
                'code' => 200,
                'message' => "realEstate Added successfully."
            ], 200);
        } else {
            return response()->json([
                'code' => 403,
                'message' => "you don't have access to this route."
            ], 200);
        }
    }

    public function realEstateRooms($id)
    {
        $estate = RealEstate::where('id', $id)->with('rooms.images')->first();
        if (isset($estate)) {
            return response()->json([
                'code' => 200,
                'message' => 'found',
                'estate' => $estate
            ], 200);
        } else {
            return response()->json([
                'code' => 404,
                'message' => "The Real Estate you requested not found.",
                'estate' => null
            ], 200);
        }
    }

    public function getMyRealEstate()
    {
        $user = Auth::user();
        if ($user->type == 'Owner') {
            return response([
                'code' => 200,
                "messages" => '',
                'estates' => RealEstate::where('user_id', $user->id)->with('owner', 'rooms.images')->get()
            ], 200);
        } else {
            return response()->json([
                'code' => 403,
                'message' => "you don't have access to this route.",
                'estates' => []
            ], 200);
        }
    }
    public function addRealEstateRoom(Request $request, $id)
    {
        // request : [ room_name  , images[files] ]
        $user = Auth::user();
        if ($user->type == 'Owner') {
            // get the real estate by id
            $realEstate = RealEstate::where('id', $id)->first();
            if (!isset($realEstate)) {
                return response()->json([
                    'code' => 404,
                    'message' => "this real estate not found."
                ], 200);
            }
            // check if this real estate owner is this user
            if ($realEstate->user_id != $user->id) {
                return response()->json([
                    'code' => 403,
                    'message' => "this real estate dosen't belongs to you."
                ], 200);
            }
            $room = RealEstateRooms::create([
                'real_estate_id' => $realEstate->id,
                'name' => $request->room_name
            ]);
            info($request->file('images'));
            $imagesUrl = [];
            $i = 1;
            if ($images = $request->file('images')) {
                foreach ($images as $image) {
                    $file_extention = $image->getClientOriginalExtension();
                    $file_name = (time() + $i++) . '.' . $file_extention;  // 546165165.jpg
                    $path = '/public/images/rooms';
                    $image->storeAs($path, $file_name);
                    $realImage = RealEstateRoomImages::create([
                        'img_url' => '/storage/images/rooms/'  . $file_name,
                        'room_id' => $room->id
                    ]);
                    array_push($imagesUrl, $realImage->img_url);
                }
            }
            return response()->json([
                'code' => 200,
                'message' => "room added successfully.",
                'room' => [
                    'name' => $room->name,
                    'images' => $imagesUrl
                ]
            ], 200);
        } else {
            return response()->json([
                'code' => 403,
                'message' => "you don't have access to this route."
            ], 200);
        }
    }
    public function estateDetails($id)
    {
        $estate = RealEstate::where('id', $id)
            ->with(['owner', 'rooms.images'])
            ->first();

        return response()->json($estate, 200);
    }
    public function editEstateImage(Request $request)
    {

        $estate = RealEstate::where("id", $request->id)->first();
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $file_extention = $image->getClientOriginalExtension();
            $file_name = time() . '.' . $file_extention;  // 546165165.jpg
            $path = '/public/images/real_estates';
            $image->storeAs($path, $file_name);
            $estate->img_url = '/storage/images/real_estates/' . $file_name;
            $estate->save();
            return response()->json([
                'code' => 200,
                'message' => "edited successfully"
            ], 200);
        } else  return response()->json([
            'code' => 300,
            'message' => "no image found"
        ], 200);
    }

    public function editVideo360(Request $request)
    {

        $estate = RealEstate::where("id", $request->id)->first();
        if ($request->hasFile('video')) {
            $image = $request->file('video');
            $file_extention = $image->getClientOriginalExtension();
            $file_name = time() . '.' . $file_extention;  // 546165165.jpg
            $path = '/public/images/360';
            $image->storeAs($path, $file_name);
            $estate->img360_url = '/storage/images/360/' . $file_name;
            $estate->save();
            return response()->json([
                'code' => 200,
                'message' => "edited successfully"
            ], 200);
        } else  return response()->json([
            'code' => 300,
            'message' => "no image found"
        ], 200);
    }


    function uploadRoomImages(Request $request)
    {
        $room = realEstateRooms::where('real_estate_id', $request->estate_id)
            ->where('name', $request->room_name)->first();
        if (!isset($room)) {
            $room = RealEstateRooms::create([
                'name' => $request->room_name,
                'real_estate_id' => $request->estate_id
            ]);
        }
        if ($request->hasFile('image')) {
            $roomsIndex = 0;
            $images = $request->file('image');

            foreach ($images as $image) {
                $file_extention = $image->getClientOriginalExtension();
                $file_name = (time() + $roomsIndex++) . '.' . $file_extention;  // 546165165.jpg
                $path = '/public/images/rooms';
                $image->storeAs($path, $file_name);
                $roomImage = RealEstateRoomImages::create([
                    'room_id' => $room->id,
                    'img_url' => '/storage/images/rooms/' . $file_name
                ]);
                info($roomImage->img_url);
                info($roomsIndex);
            }
        }
        return response()->json([
            'code' => 200,
            'message' => "all images saved."
        ], 200);
    }
    function filterByBuyType(Request $request)
    {
        $type = $request->type;
        $estates = RealEstate::where('buy_type', $type)->with('owner', 'rooms.images')->get();
        return response()->json($estates, 200);
    }
    function filterByEstateType(Request $request)
    {
        $type = $request->type;
        $estates = RealEstate::where('type', $type)->with('owner', 'rooms.images')->get();
        return response()->json($estates, 200);
    }
    function getRealEstateInsideCircle(Request $request)
    {
        $centerLat = $request->p_lat;
        $centerLng = $request->p_long;
        $estates = $this->findRealEstates(
            $centerLat,
            $centerLng
        );
        return response()->json($estates, 200);
    }

    public function generateRandomPoint($centre, $radius)
    {
        $longitude = (float) $centre[1];
        $latitude = (float)  $centre[0];
        $radius = rand(1, 10); // in miles

        $lng_min = $longitude - $radius / abs(cos(deg2rad($latitude)) * 69);
        $lng_max = $longitude + $radius / abs(cos(deg2rad($latitude)) * 69);
        $lat_min = $latitude - ($radius / 69);
        $lat_max = $latitude + ($radius / 69);

        // info('lng (min/max): ' . $lng_min . '/' . $lng_max);
        // info('lat (min/max): ' . $lat_min . '/' . $lat_max);
        return [[$lng_min, $lng_max], [$lat_min, $lat_max]];
    }
    function generateRealEstates(Request $request)
    {
        $lat = $request->lat;
        $lng = $request->lng;

        for ($i = 0; $i < 20; $i++) {
            $points = $this->generateRandomPoint(array($lat, $lng), 1);
            RealEstate::create([
                'type' => 'منزل',
                'buy_type' => 'رهن',
                'direction' => 'شمال',
                'budget' => rand(100000, 10000000),
                'lng' => $points[0][0],
                'lat' => $points[1][0],
                'user_id' => 2,
                'img_url' => 'https://picsum.photos/200/300?random=' . $i,
                'img360_url' => '',
                'location' => 'address ' . $i,
                'area' => rand(50, 120),
            ]);
            RealEstate::create([
                'type' => 'منزل',
                'buy_type' => 'رهن',
                'direction' => 'شمال',
                'budget' => rand(100000, 10000000),
                'lng' => $points[0][1],
                'lat' => $points[1][1],
                'user_id' => 2,
                'img_url' => 'https://picsum.photos/200/300?random=' . $i,
                'img360_url' => '',
                'location' => 'address ' . $i,
                'area' => rand(50, 120),
            ]);
        }
    }


    public function findRealEstates($latitude, $longitude, $radius = 5000)
    {
        /*
         * using eloquent approach, make sure to replace the "Restaurant" with your actual model name
         * replace 6371000 with 6371 for kilometer and 3956 for miles
         */
        $estates = RealEstate::selectRaw("*,
                         ( 6371000 * acos( cos( radians(?) ) *
                           cos( radians( lat ) )
                           * cos( radians( lng ) - radians(?)
                           ) + sin( radians(?) ) *
                           sin( radians( lat ) ) )
                         ) AS distance", [$latitude, $longitude, $latitude])
            ->having("distance", "<", $radius)
            ->orderBy("distance", 'asc')
            ->offset(0)
            ->limit(20)
            ->with('owner', 'rooms.images')
            ->get();
        info(count($estates));
        return $estates;
    }

    public function filterFull(Request $request)
    {
        $buyType = $request->buyType;
        $estateType = $request->estateType;
        $priceRange = $request->priceRange;
        $direction = $request->direction;
        info($request->all());
        if (!isset($buyType)) {
            $buyType = '%';
        }
        if (!isset($estateType)) {
            $estateType = '%';
        }
        if (!isset($direction)) {
            $direction = '%';
        }
        if ($priceRange[0] == 0.0 && $priceRange[1] == 0.0) {
            $priceRange[0] = 0;
            $priceRange[1] = 1000000000;
        }
        $estates = RealEstate::where('buy_type', 'like', $buyType)
            ->where('type', 'like', $estateType)
            ->where('direction', 'like', $direction)
            ->whereBetween('budget', $priceRange)
            ->with('owner', 'rooms.images')
            ->get();
        info($estates);
        return response()->json($estates, 200);
    }
}
