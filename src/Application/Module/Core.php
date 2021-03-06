<?php
namespace Application\Module;

use Application\Delivery\Middleware\Authentication;
use Aura\Di\Container;
use Aura\Session\SessionFactory;
use Cadre\Module\Module;
use Application\Delivery\Input\Generic as GenericInput;
use Application\Delivery\Responder\Generic as GenericResponder;
use Psr7Middlewares\Middleware\AttributeMapper;
use Psr7Middlewares\Middleware\AuraSession;
use Psr7Middlewares\Middleware\Robots;
use Psr7Middlewares\Middleware\TrailingSlash;
use Radar\Adr\Handler\RoutingHandler;
use Radar\Adr\Handler\ActionHandler;
use Relay\Middleware\ExceptionHandler;
use Relay\Middleware\ResponseSender;
use Zend\Diactoros\Response;

class Core extends Module
{
    public function require()
    {
        return [
            AtlasOrm::class,
            Tactician::class,
            Twig::class,
        ];
    }

    public function define(Container $di)
    {
        /** DefaultResponder */

        $di->params[GenericResponder::class] = [
            'twig' => $di->lazyGet('twig:environment'),
        ];

        /** ExceptionHandler */

        $di->params[ExceptionHandler::class] = [
            'exceptionResponse' => $di->lazyNew(Response::class),
        ];

        /** AuraSession */

        $di->params[AuraSession::class] = [
            'factory' => $di->lazyNew(SessionFactory::class),
        ];

        $di->setters[AuraSession::class] = [
            'name' => 'pen-paper',
        ];

        /** Robots */

        $di->params[Robots::class] = [
            'allow' => !$this->loader()->isDev(),
        ];

        /** TrailingSlash */

        $di->params[TrailingSlash::class] = [
            'addSlash' => true,
        ];

        $di->setters[TrailingSlash::class] = [
            'redirect' => 301,
        ];

        /** AttributeMapper */

        $di->params[AttributeMapper::class] = [
            'mapping' => [
                AuraSession::KEY => 'session',
            ],
        ];
    }

    public function modify(Container $di)
    {
        $adr = $di->get('radar/adr:adr');

        $adr->middle(ResponseSender::class);
        $adr->middle(Robots::class);
        $adr->middle(ExceptionHandler::class);
        $adr->middle(TrailingSlash::class);
        $adr->middle(AuraSession::class);
        $adr->middle(Authentication::class);
        $adr->middle(AttributeMapper::class);
        $adr->middle(RoutingHandler::class);
        $adr->middle(ActionHandler::class);

        $adr->input(GenericInput::class);
        $adr->responder(GenericResponder::class);
    }
}
