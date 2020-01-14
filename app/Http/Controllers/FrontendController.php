<?php

namespace App\Http\Controllers;

use App\Chargeplace;
use App\File;
use Illuminate\Http\Request;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\DB;
class FrontendController extends Controller
{
    public function index(){
        $points = Chargeplace::all();
        $files = File::all();
        return view('welcome')
            ->with('files',$files)
            ->with('points',$points);
    }

    public function getNearbyChargers(Request $request){
        $lat = $request->lat;
        $lng = $request->lng;
        $points = DB::select("SELECT id, ST_AsText(location) AS point, name, ROUND((ST_Distance(POINT($lng,$lat), location)*111195)/1000, 2) AS distance FROM chargeplaces
WHERE ROUND((ST_Distance(POINT($lng,$lat), location)*111195)/1000, 2) < 4.5
ORDER BY distance ASC");
        return response()->json(json_encode($points),200);
    }

    public function addPlace(Request $request){
        $chargeplace = new Chargeplace();
        $chargeplace->name = $request->name;
        // saving a point
        $chargeplace->location = new Point($request->latitude, $request->longitude);	// (lat, lng)
        $chargeplace->save();

        return response()->json(json_encode([$request->latitude, $request->longitude]));
    }
}
