<?php

namespace App\Exceptions;

use Exception;

class ShortLinkNotFoundException extends Exception
{
    protected $message = 'Short Link not found';
    protected $code = 404;
}
