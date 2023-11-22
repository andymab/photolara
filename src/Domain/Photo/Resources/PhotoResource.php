<?php
namespace Domain\Photo\Resources;


use App\Models\Photo;
use Illuminate\Http\Resources\Json\JsonResource;

class PhotoResource extends JsonResource 
{
    /**
     * @param $request
     * @return array
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function toArray($request)
    {
    
        /** @var Photo $photo */
        $photo = $this->resource;

        return [
            'id' => $photo->id,
            'title' => $photo->title,
            'descr' => $photo->descr,
            'src_small' => $photo->src_small,
            'src_big'  => $photo->src_big,
        ];
    }

}