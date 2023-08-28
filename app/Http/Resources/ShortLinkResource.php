<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShortLinkResource extends JsonResource
{
    
    /**
     * @OA\Schema(
     *     schema="ShortLinkResource",
     *     title="ShortLinkResource",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="original_link", type="string"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     *     @OA\Property(property="deleted_at", type="string", format="date-time"),
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user_id,
            'original_url' => $this->original_url,
            'slug' => $this->short_code,
            'access' => $this->access_count,
            'expiration_date' => $this->expiration_date,
            'access_log' => AccessLogResource::collection($this->whenLoaded('accessLogs')),
            'created_at' =>$this->created_at->format('d-m-Y:i:s'),
            'updated_at' =>$this->updated_at->format('d-m-Y:i:s'),
        ];
    }
}
