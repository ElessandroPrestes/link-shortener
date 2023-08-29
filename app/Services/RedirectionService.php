<?php

namespace App\Services;

use App\Interfaces\Services\RedirectionServiceInterface;
use App\Repositories\ShortLinkRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RedirectionService implements RedirectionServiceInterface
{
    protected $shortLinkRepository;

    public function __construct(ShortLinkRepository $shortLinkRepository)
    {
        $this->shortLinkRepository = $shortLinkRepository;
    }

    public function redirectToOriginalUrl(string $slug, Request $request)
    {
            $shortCodes = $this->shortLinkRepository->searchCode($slug);
            
            if ($shortCodes->isEmpty()) {
                throw new NotFoundHttpException('Short link not found');
            }

            $shortCode = $shortCodes->first();
           
            $this->shortLinkRepository->incrementAccessCount($shortCode->id);

            return $shortCode->original_url;
    }
}