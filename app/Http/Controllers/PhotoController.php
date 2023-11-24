<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoItem;
use Domain\Photo\Resources\PhotoResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Intervention\Image\Facades\Image as ResizeImage;

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

        $query = Photo::query()->where('user_id', auth()->user()->id)->when($request->get('search'), function ($query, $search) {
            return $query->where('title', 'LIKE', "%$search%");
        });
        $data = PhotoResource::collection($query->get())->toArray($request);
        return Inertia::render('Photo/Index', [
            'data' => $data// array_chunk($data,4)
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function getItems(Photo $photo, Request $request)
    {
        $query = PhotoItem::query()->where('photo_id', $photo->id)->when($request->get('search'), function ($query, $search) {
            return $query->where('title', 'LIKE', "%$search%");
        });

        $data = PhotoResource::collection($query->get())->toArray($request);

        return Inertia::render('Photo/Index', [
            'photo' => $photo,
            'data' => $data,
        ]);
    }

    public function storeItems(Photo $photo, Request $request)
    {

        //type_image = square/rectangle
        //type_photos = cover/page
        $this->validate($request, [
            'data' => ['required'],
            'base64' => ['required'],
            'type_photos' => ['required'],
            'type_image' => ['required'],
        ]);


        try {
            $form = json_decode($request->data);
        } catch (\Throwable $th) {
            return $th;
        }

        if (empty($form->title)) {
            return;
        }

        $data = [
            'photo_id' => $photo->id,
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

        $image = null;
        if (mb_strpos($request->base64, 'base64')) {
            $image = $this->createImageJPG($request->base64, $request->type_image);
            list($type, $extend) = explode("/", $image->mime());
            if ($type != 'image') {
                return;
            }
        } else {
            $photo->save();
            return Response::json($photo);
        }

        if ($image) {
            $path = storage_path('app/public/photos') . '/' . auth()->user()->id . '/' . $photo->id . '/' . $photoItem->id;

            !is_dir($path) &&
                mkdir($path, 0777, true);


            $pathfile = 'photos/' . auth()->user()->id . '/' . $photo->id . '/' . $photoItem->id . '/' . $request->type_image . '-' . $photoItem->id . '-' . time() . "." . $extend;
            
            $image->save($path . '/' . $request->type_image . '-' . $photoItem->id . '-' . time() . "." . $extend, 90);
            

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

        return Response::json($photoItem);
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

        $image = null;
        if (mb_strpos($request->base64, 'base64')) {
            $image = $this->createImageJPG($request->base64, $request->type_image);            
            list($type, $extend) = explode("/", $image->mime());
            if ($type != 'image') {
                return;
            }            
        } else {
            $photo->save();
            return Response::json($photo);
        }
 

        if ($image) {
            $path = storage_path('app/public/photos') . '/' . auth()->user()->id . '/' . $photo->id;

            !is_dir($path) &&
                mkdir($path, 0777, true);


            $pathfile = 'photos/' . auth()->user()->id . '/' . $photo->id . '/' . $request->type_image . '-' . $photo->id . '-' . time() . "." . $extend;

            $image->save($path . '/' . $request->type_image . '-' . $photo->id . '-' . time() . "." . $extend, 90);

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
    }

    public function createImageJPG($src, $type)
    {
        $image = ResizeImage::make($src)->encode('jpg', 85);

        $heigth =  $image->height();
        $width = $image->width();

        if ($type == 'square') {

            if ($width > $heigth) {
                if ($width > 800) {
                    $image->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
            } else {
                if ($heigth > 800) {
                    $image->resize(null, 800, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
            }
        } else {
            if ($width > $heigth) {
                if ($width > 1200) {
                    $image->resize(1200, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
            } else {
                if ($heigth > 1200) {
                    $image->resize(null, 1200, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
            }
        }


        return $image;
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
