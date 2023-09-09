<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ShortLinkNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShortLinkRequest;
use App\Http\Requests\UpdateShortLinkRequest;
use App\Http\Resources\ShortLinkResource;
use App\Interfaces\Services\RedirectionServiceInterface;
use App\Interfaces\Services\ShortLinkServiceInterface;
use App\Services\ShortLinkService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
   
    public function __construct(
        protected ShortLinkService $shortLinkService,
        protected RedirectionServiceInterface $redirectionService
    )
    {
        $this->shortLinkService = $shortLinkService;
        $this->redirectionService = $redirectionService;
    }
    
    /**
     * @OA\Get(
     *      path="/api/v1/links",
     *      operationId="getAllLinks",
     *      tags={"Links"},
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
        ], Response::HTTP_OK);
    }

    /**
    * @OA\Post(
    *      path="/api/v1/links",
    *      tags={"Links"},
    *      description="Create Link",
    *      @OA\RequestBody(
    *          required=true,
    *          @OA\JsonContent(
    *              type="object",
    *              @OA\Property(property="original_url", type="string", example="bit.ly/3LaMLtZ"),
    *          )
    *      ),
    *      @OA\Response(
    *          response=201,
    *          description="Short Link Registered",
    *          @OA\JsonContent(
    *              type="object",
    *              @OA\Property(property="message", type="string", example="Short Link Registered")
    *          )
    *      ),
    *      @OA\Response(
    *          response=400,  
    *          description="Bad Request",
    *          @OA\JsonContent(
    *              type="object",
    *              @OA\Property(property="message", type="string", example="Error Processing The Request")
    *          )
    *      ),
    *      @OA\Response(
    *          response=422,
    *          description="Unprocessable Entity",
    *          @OA\JsonContent(
    *              type="object",
    *              @OA\Property(property="message", type="string", example="Unprocessable Entity")
    *          )
    *      ),
    * )
    */
    public function store(StoreShortLinkRequest $request)
    {
        try {
            $this->shortLinkService->storeLink($request->validated());
    
            return response([
                'message' => 'Short Link Registered'
            ],  Response::HTTP_CREATED);
        } catch (BadRequestException $e) {
            return response([
                'message' => $e->getMessage() 
            ], 400);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/v1/links/{id}", 
    *     tags={"Links"},
    *     description="Retrieve a Short Link by Id.",
    *     operationId="getLinkById",
    *     @OA\Parameter(
    *         name="ID", 
    *         in="path", 
    *         required=true, 
    *         description="ID of the Short Link to be retrieved.",
    *         @OA\Schema(
    *             type="string" 
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Short Link listed.",
    *         @OA\JsonContent(
    *            type="object",
    *              @OA\Property(property="message", type="string", example="Short Link Listed")
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,description="Short Link Not Found",
    *          @OA\JsonContent(
    *              type="object",
    *              @OA\Property(property="message", type="string", example="Short Link Not Found")
    *          )
    *     ),
    * )
    */
    public function show($id)
    {
        try {
            $link = $this->shortLinkService->showLink($id);
    
            return response([
                'data' => new ShortLinkResource($link),
                'message' => 'Short Link listed'
            ], Response::HTTP_OK);
        }catch (BadRequestHttpException $e) { 
                return response([
                    'message' => $e->getMessage() 
                ], Response::HTTP_BAD_REQUEST);
        } catch (ShortLinkNotFoundException $e) {
            return response([
                'message' => $e->getMessage()
            ], $e->getCode());
        } catch (\Exception $e) {
            return response([
                'message' => 'An error occurred'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/links/{id}",
     *     tags={"Links"},
     *     description="Update Link",
     *     operationId="update",
     *     @OA\Parameter(
     *         name="ID", 
     *         in="path", 
     *         required=true, 
     *         description="Link to be updated."
     *     ),
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              ref="#/components/schemas/ShortLinkResource"
     *          )
     *     ),
     *      @OA\Response(
     *         response=403,description="Forbidden",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",type="string",example="Forbidden"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,description="Short Link Updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",type="string",example="Short Link Updated"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=422,description="Unprocessable Entity",
     *          @OA\JsonContent(
     *              type="object",
     *                  @OA\Property(property="message", type="string", example="Unprocessable Entity")
     *          )
     *       ),
      *      @OA\Response(
     *         response=402,description="Bad request",
     *          @OA\JsonContent(
     *              type="object",
     *                  @OA\Property(property="message", type="string", example="Bad request")
     *          )
     *     ),
      *     @OA\Response(
     *         response=404,description="Short Link Not Found",
     *          @OA\JsonContent(
     *              type="object",
     *                  @OA\Property(property="message", type="string", example="Short Link Not Found")
     *          )
     *     ),
     * )
     */
    public function update(UpdateShortLinkRequest $request, int $id)
    {
       $this->shortLinkService->updateLink($id, $request->validated());
      
        return response()([
            'message' => 'Short Link Updated'
        ], Response::HTTP_OK);
    }

    /**
 * @OA\Delete(
 *     path="/api/v1/links/{id}",
 *     tags={"Links"},
 *     description="Delete a short link",
 *     operationId="destroyShortLink",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The short link to delete",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="No content",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Short link not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Short link not found")
 *         )
 *     ),
 * )
 */
    public function destroy(string $id)
    {
        $this->shortLinkService->destroyLink($id);

        return response()([
            'message' => 'Deleted'
        ], Response::HTTP_NO_CONTENT);
    }

    /**
    * @OA\Get(
    *     path="/api/v1/links/{link}", 
    *     tags={"Links"},
    *     description="Retrieve a Short Link by Text.",
    *     operationId="searchText",
    *     @OA\Parameter(
    *         name="ID", 
    *         in="path", 
    *         required=true, 
    *         description="Text of the Short Code to be retrieved.",
    *         @OA\Schema(
    *             type="string" 
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Short Link listed.",
    *         @OA\JsonContent(
    *            type="array",
    *              @OA\Items(ref="#/components/schemas/ShortLinkResource")
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,description="Short Code Not Found",
    *          @OA\JsonContent(
    *              type="object",
    *              @OA\Property(property="message", type="string", example="Short Code Not Found")
    *          )
    *     ),
    * )
    */
    public function searchCode(string $slug)
    {

        try {
            $shortCode = $this->shortLinkService->searchCode($slug);

            return response([
                'data'=> ShortLinkResource::collection($shortCode),
                'message' => 'Short Code listed BY Text'
            ], Response::HTTP_OK);
        } catch (ShortLinkNotFoundException $e) {
            return response([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
        
    }

    /**
    * @OA\Get(
    *     path="/api/v1/redirect/{slug}",
    *     operationId="redirectToOriginalUrl",
    *     description="Redirect to the original URL",
    *     tags={"Redirect"},
    *     @OA\Parameter(
    *         name="slug",
    *         in="path",
    *         required=true,
    *         @OA\Schema(type="string"),
    *         description="Short link slug"
    *     ),
    *     @OA\Response(
    *         response=302,
    *         description="Redirects to the original URL",
    *         @OA\Header(
    *             header="Location",
    *             description="Original URL",
    *             @OA\Schema(type="string")
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Short link not found"
    *     )
    * )
    */
    public function redirectToOriginalUrl(string $slug, Request $request)
    {
        try {
            $originalUrl = $this->redirectionService->redirectToOriginalUrl($slug, $request);
            return redirect()->away($originalUrl, Response::HTTP_FOUND);
        } catch (\Throwable $th) {
            throw new NotFoundHttpException('Short link not found');
        }
        
    }
}
