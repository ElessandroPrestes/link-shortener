<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShortLinkRequest;
use App\Http\Resources\ShortLinkResource;
use App\Services\ShortLinkService;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *      title="Household appliances",
 *      version="1.0.0",
 *      description="API for short links",
 *      @OA\Contact(
 *          email="elessandrodev@gmail.com"
 *      ),
 *      @OA\License(
 *          name="MIT"
 *      )
 * )
 */

class ShortLinkController extends Controller
{
    protected $shortLinkService;

    public function __construct(ShortLinkService $shortLinkService)
    {
        $this->shortLinkService = $shortLinkService;
    }
    
    /**
     * @OA\Get(
     *      path="/api/v1/links",
     *      operationId="getAllLinks",
     *      tags={"brands"},
     *      description="Returns list of links",
     *      @OA\Response(
     *          response=200,
     *          description="Links listed."
     *       )
     *     ),
     *      @OA\Server(
     *          url="http://localhost:8000",
     * 
     *      )
     *
     *@return ShortLinkResource
     */
    public function index()
    {
        $links = $this->shortLinkService->indexLinks();

        return response([
            'data' => ShortLinkResource::collection($links),
            'message' => 'Links listed'
        ], 200);
    }

    /**
    * @OA\Post(
    *      path="/api/v1/links",
    *      tags={"Link"},
    *      description="Create Link",
    *      @OA\RequestBody(
    *          required=true,
    *          @OA\JsonContent(
    *               type="object",
    *               @OA\Property(property="original_url", type="string", example="bit.ly/3LaMLtZ"),
    *       )
    *     ),
    *       @OA\Response(
    *          response=201,description="Link Register.",
    *           @OA\JsonContent(
    *              type="array",
    *              @OA\Items(ref="#/components/schemas/ShortLinkResource")
    *          )
    *      ),
    *       @OA\Response(
    *          response=422,description="Unprocessable Entity",
    *          @OA\JsonContent(
    *               type="object",
    *              @OA\Property(property="message", type="string", example="Unprocessable Entity")
    *           )
    *       ),
    *     ),
    */
    public function store(StoreShortLinkRequest $request)
    {
        $shortLink = $this->shortLinkService->storeLink($request->validated());

        return response([
            'data'=>new ShortLinkResource($shortLink),
            'message' => 'Link Register'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
