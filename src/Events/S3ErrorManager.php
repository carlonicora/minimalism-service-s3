<?php
namespace CarloNicora\Minimalism\Services\S3\Events;

use Aws\S3\Exception\S3Exception;
use CarloNicora\Minimalism\Core\Events\Abstracts\AbstractErrorEvent;
use CarloNicora\Minimalism\Core\Events\Interfaces\EventInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;

class S3ErrorManager extends AbstractErrorEvent
{
    /** @var string  */
    protected string $serviceName = 'aws';

    /**
     * @param string $config
     * @return EventInterface
     */
    public static function CONFIGURATION_ERROR(string $config) : EventInterface
    {
        return new self(1, ResponseInterface::HTTP_STATUS_500, $config . ' is a required configuration');
    }

    /**
     * @param S3Exception $exception
     * @return EventInterface
     */
    public static function S3_EXCEPTION(S3Exception $exception): EventInterface
    {
        return new self(2, ResponseInterface::HTTP_STATUS_500, 'Failed to upload an image', null, $exception);
    }
}