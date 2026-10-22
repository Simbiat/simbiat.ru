<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\Api\Api;

final class Upload extends Api
{
    /**
     * @var bool Flag to indicate, that this is the lowest level
     */
    #[\Override]
    protected bool $final_node = true;

    /**
     * @var array<string, string> Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
     */
    #[\Override]
    protected array $methods = ['POST' => ''];

    /**
     * @var bool Flag indicating that authentication is required
     */
    #[\Override]
    protected bool $authentication_needed = true;

    protected function genData(array $path): array
    {
        try {
            $upload = new Curl()->upload();
            if (!empty($upload['http_error'])) {
                return $upload;
            }

            return ['response' => true, 'location' => $upload['location']];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return ['http_error' => 500, 'reason' => 'Failed to upload file'];
        }
    }
}
