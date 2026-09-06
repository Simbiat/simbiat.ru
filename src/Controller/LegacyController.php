<?php
declare(strict_types = 1);

namespace App\Controller;

use App\HomePage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/{path}', name: 'app_legacy', requirements: ['path' => '.*'], priority: -100)]
    public function index(): Response
    {
        new HomePage();

        #Unreachable: HomePage::twigProc() always calls exit(0).
        return new Response();
    }
}
