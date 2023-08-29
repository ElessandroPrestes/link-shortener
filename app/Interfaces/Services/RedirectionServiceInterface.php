<?php

namespace App\Interfaces\Services;

use Illuminate\Http\Request;

interface RedirectionServiceInterface
{
    public function redirectToOriginalUrl(string $slug, Request $request);
}