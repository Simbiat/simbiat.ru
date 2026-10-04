<?php

declare(strict_types=1);

namespace App\Controller;

use App\HomePage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Temporary bridge controller. Hands every request straight to the legacy
 * HomePage flow, unchanged. HomePage handles headers, session, DB, Twig
 * rendering, and output itself, and always terminates via `exit(0)`, so this
 * action never actually returns a Response - Symfony's router just needs
 * something to dispatch to.
 *
 * App bootstrap (Config, error handlers) now happens once in Kernel::boot(),
 * not here - see App\Kernel.
 *
 * Remove this once routing is migrated to real Symfony controllers.
 */
final class LegacyController
{
    /**
     * Old page bootstrap
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/{path}', name: 'app_legacy', requirements: ['path' => '.*'], priority: -100)]
    public function index(Request $request): Response
    {
        $method = \strtoupper($request->getMethod());
        // Parse multipart/form-data for PUT/DELETE/PATCH methods (if any). Do not use Headers function, since the body is already consumed.
        if (\in_array($method, ['PUT', 'DELETE', 'PATCH'], true)) {
            $_POST = $request->request->all();
            $_FILES = $request->files->all();
        }
        new HomePage();

        // Unreachable: HomePage::twigProc() always calls exit(0).
        return new Response();
    }
}
