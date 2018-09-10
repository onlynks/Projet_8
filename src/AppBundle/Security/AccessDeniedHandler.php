<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler extends FindRedirect implements AccessDeniedHandlerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $route = $this->getRedirectPath($request->getPathInfo());
        $request->getSession()->getFlashbag()->add('error', 'Vous n\'avez pas les droits nécessaires pour supprimer cette tâche.');
        $url = $this->router->generate($route);

        return new RedirectResponse($url);
    }
}