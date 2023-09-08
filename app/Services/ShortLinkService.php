<?php

namespace App\Services;

use App\Exceptions\ShortLinkNotFoundException;
use App\Models\ShortLink;
use App\Repositories\ShortLinkRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShortLinkService
{
    protected $shortLinkRepository;

    public function __construct(ShortLinkRepository $shortLinkRepository)
    {
        $this->shortLinkRepository = $shortLinkRepository;
    }

    public function indexLinks()
    {
        return $this->shortLinkRepository->getAllLinks();
    }

    public function storeLink(array $data)
    {
        return $this->shortLinkRepository->createLink($data);
    }

    public function validatePositiveIntegerId($id)
    {
        if (!ctype_digit($id) || intval($id) <= 0) {
            throw new BadRequestHttpException('ID must be a positive integer');
        }
    }

    public function showLink(int $id)
    {
        try {

            $this->validatePositiveIntegerId($id);
        
            return $this->shortLinkRepository->getLinkById($id);


        } catch (ModelNotFoundException $e) {
            throw new ShortLinkNotFoundException();
        }
    }

    public function updateLink(int $id, array $data)
    {
        if ($this->shortLinkRepository->getLinkById($id)) {
            if (!isset($data['short_code'])) {
                $data['short_code'] = Str::random(rand(6, 8));
            }

            return $this->shortLinkRepository->updateLink($id, $data);
        } else {
            throw new ShortLinkNotFoundException();
        }
    }

    public function destroyLink(int $id)
    {
        return $this->shortLinkRepository->deleteLink($id);
    }

    public function searchCode(string $slug)
    {
        try {
            return $this->shortLinkRepository->searchCode($slug);
        } catch (\Throwable $th) {
            throw new ShortLinkNotFoundException();
        }
    }

    
}
