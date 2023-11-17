<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
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
        $photos = Photo::where('user_id', auth()->user()->id)->get();
        return Inertia::render('Photo/Index', [
            'data' => $photos
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function getItems(Photo $photo)
    {
       
        return Inertia::render('Photo/Index', [
            'photo'=> $photo,
            'data' => $photo->photos
        ]);
    }   
    
    public function storeItems(Photo $photo,Request $request){

        //type_image = square/rectangle
        //type_photos = cover/page
        $this->validate($request, [
            'data' => ['required'],
            'base64' => ['required'],
            'type_photos' => ['required'],
            'type_image' => ['required'],
        ]);

        $image = null;
        if (mb_strpos($request->base64, 'base64')) {
            list($type, $imagedata) = explode(",", $request->base64);
            $image = base64_decode($imagedata);
            $extend = mb_strpos($type, 'jpeg') ? 'jpeg' : (mb_strpos($type, 'png') ? 'png' : false);

            if (!$extend) {
                return;
            }
        }


        try {
            $form = json_decode($request->data);
        } catch (\Throwable $th) {
            return $th;
        }

        if (empty($form->title)) {
            return;
        }

        $data = [
            'photo_id'=>$photo->id,
            'title' => $form->title,
            'descr' => $form->descr,
            'user_id' => auth()->user()->id,
        ];

        if (!$form->id) {
            $photoItem = PhotoItem::create($data);
        } else {
            $photoItem = PhotoItem::find($form->id);
            $photoItem->title = $form->title;
            $photoItem->descr =  $form->descr;
        }
        if ($image) {
            $path = storage_path('app/public/photos') . '/' . auth()->user()->id . '/' . $photo->id . '/' . $photoItem->id;

            !is_dir($path) &&
                mkdir($path, 0777, true);


            $pathfile = 'photos/' . auth()->user()->id . '/' . $photo->id . '/' . $photoItem->id . '/' . $request->type_image . '-' . $photoItem->id . '-' . time() . "." . $extend;

            Storage::disk('public')->put($pathfile, $image);

            if ($request->type_image == 'square') {
                if ($photoItem->src_small) {
                    $this->deleteImage(str_replace("/storage/", "", $photoItem->src_small));
                }
                $photoItem->src_small = "/storage/" . $pathfile;
            } else {
                if ($photoItem->src_big) {
                    $this->deleteImage(str_replace("/storage/", "", $photoItem->src_big));
                }

                $photoItem->src_big = "/storage/" . $pathfile;
            }
        }

        $photoItem->save();
        return Response::json($photo);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        //type_image = square/rectangle
        //type_photos = cover/page
        $this->validate($request, [
            'data' => ['required'],
            'base64' => ['required'],
            'type_photos' => ['required'],
            'type_image' => ['required'],
        ]);

        $image = null;
        if (mb_strpos($request->base64, 'base64')) {
            list($type, $imagedata) = explode(",", $request->base64);
            $image = base64_decode($imagedata);
            $extend = mb_strpos($type, 'jpeg') ? 'jpeg' : (mb_strpos($type, 'png') ? 'png' : false);

            if (!$extend) {
                return;
            }
        }


        try {
            $form = json_decode($request->data);
        } catch (\Throwable $th) {
            return $th;
        }

        if (empty($form->title)) {
            return;
        }

        $data = [
            'title' => $form->title,
            'descr' => $form->descr,
            'user_id' => auth()->user()->id,
        ];

        if (!$form->id) {
            $photo = Photo::create($data);
        } else {
            $photo = Photo::find($form->id);
            $photo->title = $form->title;
            $photo->descr =  $form->descr;
        }
        if ($image) {
            $path = storage_path('app/public/photos') . '/' . auth()->user()->id . '/' . $photo->id;

            !is_dir($path) &&
                mkdir($path, 0777, true);


            $pathfile = 'photos/' . auth()->user()->id . '/' . $photo->id . '/' . $request->type_image . '-' . $photo->id . '-' . time() . "." . $extend;

            Storage::disk('public')->put($pathfile, $image);

            if ($request->type_image == 'square') {
                if ($photo->src_small) {
                    $this->deleteImage(str_replace("/storage/", "", $photo->src_small));
                }
                $photo->src_small = "/storage/" . $pathfile;
            } else {
                if ($photo->src_big) {
                    $this->deleteImage(str_replace("/storage/", "", $photo->src_big));
                }

                $photo->src_big = "/storage/" . $pathfile;
            }
        }

        $photo->save();

        return Response::json($photo);

        // 'title',
        // 'descr',
        // 'user_id',
        // 'src_small',
        // 'src_big',
    }

    public function deleteImage($srcimage)
    {
        if (Storage::disk('public')->exists($srcimage)) {
            Storage::disk('public')->delete($srcimage);
            /*
                Delete Multiple files this way
                Storage::delete(['upload/test.png', 'upload/test2.png']);
            */
            return true;
        } else {
            return false;
        }
    }

}
