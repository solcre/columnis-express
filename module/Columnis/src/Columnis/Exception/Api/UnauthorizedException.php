<?php

/**
 * Description of UnauthorizedException
 *
 * @author matias
 */

namespace Columnis\Exception\Api;

use Columnis\Exception\Exception;

class UnauthorizedException extends \Exception implements Exception
{
    /**
     *
     * @var \Exception
     */
    private $previous;
    
    /**
     * 
     * @param string $message
     * @param string|long $code
     * @param \Exception $previous
     */
    public function __construct ($message, $code, \Exception $previous)
    {
        $this->message = $message;
        $this->code = $code;
        $this->previous = $previous;
    }

}
