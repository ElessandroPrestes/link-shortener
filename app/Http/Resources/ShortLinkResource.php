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
            'original_url' => $this->original_url,
            'identifier' => $this->identifier,
            'created_at' =>$this->created_at->format('d-m-Y:i:s'),
            'updated_at' =>$this->updated_at->format('d-m-Y:i:s'),
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
