<?php

declare(strict_types=1);

namespace App;

use App\Enum\SystemUser;
use App\Service\Config;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Symfony's Kernel
 */
final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * Guards against re-running app bootstrap if boot() runs more than once in the same process.
     * Harmless today since each request/command is its own process, but relevant if FrankenPHP worker mode is adopted later.
     *
     * @var bool
     */
    private bool $app_bootstrapped = false;

    /**
     * Overriding Symfony's boot to replace the previous `Bootstrap.php`
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
        if (!$this->app_bootstrapped) {
            $this->app_bootstrapped = true;
            // Check if we are in CLI
            if (\preg_match('/^cli(-server)?$/iu', \PHP_SAPI) === 1) {
                // Impersonate system user
                $_SESSION['user_id'] = SystemUser::System->value;
                $_SESSION['username'] = 'System user';
                $_SESSION['permissions'] = ['close_own_threads', 'close_others_threads'];
            }
            // Generate basic settings
            new Config($this->getContainer());
            // Set error handling
            \set_error_handler('\App\Service\Errors::error_handler');
            \set_exception_handler('\App\Service\Errors::error_log');
            \register_shutdown_function('\App\Service\Errors::shutdown');
        }
    }

    /**
     * Temporary custom handler to process requests like `/%c0`.
     * TODO: Most likely can be removed after proper migration.
     *
     * @throws \Exception
     */
    public function handle(Request $request, int $type = HttpKernelInterface::MAIN_REQUEST, bool $catch = true): Response
    {
        if ($type === HttpKernelInterface::MAIN_REQUEST) {
            $decoded_path = \rawurldecode($request->getPathInfo());
            if (!\mb_check_encoding($decoded_path, 'UTF-8')) {
                $request = Request::create(
                    '/httperror/404',
                    $request->getMethod(),
                    server: $request->server->all(),
                );
            }
        }

        return parent::handle($request, $type, $catch);
    }

    /**
     * @return list<string> An array of allowed values for APP_ENV
     */
    private function getAllowedEnvs(): array
    {
        return ['prod', 'dev', 'test'];
    }
}
