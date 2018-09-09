<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler extends FindRedirect implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $result = $this->getRedirectPath($request->getPathInfo());

        if($result){
           $this->render(($result));
        }
        else {
            var_dump('doesn\'t work');
        }; exit;

        return new Response('', 403);
    }
}