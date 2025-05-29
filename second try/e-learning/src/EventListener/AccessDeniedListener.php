<?php // src/Security/AccessDeniedHandler.php
namespace App\EventListener;

use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class AccessDeniedListener implements AccessDeniedHandlerInterface
{
private RouterInterface $router;

public function __construct(RouterInterface $router)
{
$this->router = $router;
}

public function handle(Request $request, AccessDeniedException $accessDeniedException): Response
{
return new RedirectResponse($this->router->generate('access_denied_page'));
}
}
