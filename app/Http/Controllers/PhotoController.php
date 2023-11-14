<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PhotoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $photos = Photo::where('user_id',auth()->user()->id)->get();
        return Inertia ::render('Photo/Index', [
            'data' => $photos
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'data' => ['required'],
            'base64'=>['required'],
            'type_photo'=>['required'],
        ]);
        list($type,$imagedata) = explode(",",$request->base64);
        $image = base64_decode($imagedata);
        $extend = mb_strpos($type,'jpeg') ? 'jpeg' : (mb_strpos($type,'png') ? 'png' : false);

        if(!$extend){
            return;
        }

        try {
            $form = json_decode($request->data);
        } catch (\Throwable $th) {
            return $th;
        }

        if(empty($form->title)){
            return;
        }

        $photo = Photo::create([
        'title'=>$form->title,
        'descr'=>$form->descr,
        'user_id'=>auth()->user()->id,
        'src_small'=>'',
        'src_big'=>'',            
        ]);

      //  $path = storage_path('app/public/photos/'.auth()->user()->id."/".$photo->id);
      $path = storage_path('app/public/photos/1/1');
      log::info($path);
        !is_dir($path) &&
            mkdir($path, 0777, true);

        Storage::disk('public')->put('photos/1/1/'. $photo->id.".".$extend, $image);

        return 'ok';
        // 'title',
        // 'descr',
        // 'user_id',
        // 'src_small',
        // 'src_big',
    }

    /**
     * Display the specified resource.
     */
    public function show(Photo $photo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Photo $photo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Photo $photo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Photo $photo)
    {
        //
    }
}
