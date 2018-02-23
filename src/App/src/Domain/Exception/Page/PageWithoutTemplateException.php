<?php

/**
 * Description of PageWithoutTemplateException
 *
 * @author matias
 */

namespace App\Domain\Exception\Page;

use App\Domain\Exception\Exception;

class PageWithoutTemplateException extends \RuntimeException implements Exception
{
}
