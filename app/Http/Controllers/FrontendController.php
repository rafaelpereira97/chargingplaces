<?php

namespace App\Http\Controllers;

use App\Chargeplace;
use App\File;
use App\Polygonplace;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use Illuminate\Http\Request;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\DB;
class FrontendController extends Controller
{
    public function index(){
        $points = Chargeplace::all();
        $files = File::all();
        $polypoints = Polygonplace::all();
        return view('welcome')
            ->with('files',$files)
            ->with('points',$points)
            ->with('polypoints',$polypoints);
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

    public function createPolygon(Request $request){
        $latlngs = json_decode($request->poligono);

        $place1 = new Polygonplace();
        $place1->name = 'teste';
        $arrayOfPoints = [];
        $linestring = new LineString([]);
        foreach($latlngs as $index => $point){
            if($index == 0){
                $lat = $point->lat;
                $lng = $point->lng;
            }
            $linestring->appendPoint(new Point($point->lat,$point->lng));
        }
        $linestring->appendPoint(new Point($lat,$lng));
        $place1->area = new Polygon([$linestring]);
        $place1->save();
        return response()->json('Sucesso', 200);
    }
}
