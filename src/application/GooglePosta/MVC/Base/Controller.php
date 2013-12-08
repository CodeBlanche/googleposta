<?php

namespace GooglePosta\MVC\Base;

use Command\CommandFactory;
use Config\Config;
use Path\Resolver;
use Session\Session;
use Web\Response\Status;
use Web\Web;

/**
 * Class Controller
 *
 * @package Totally200\MVC
 */
abstract class Controller extends \MVC\Controller
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Resolver
     */
    protected $path;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var View
     */
    protected $view;

    /**
     * @var CommandFactory
     */
    protected $commandFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @param Web              $web
     * @param Model            $model
     * @param View             $view
     * @param Config           $config
     * @param Resolver         $pathResolver
     * @param CommandFactory   $commandFactory
     * @param Session $session
     */
    function __construct(
        Web $web,
        Model $model,
        View $view,
        Config $config,
        Resolver $pathResolver,
        CommandFactory $commandFactory,
        Session $session
    ) {
        parent::__construct($web, $model, $view);

        $this->config         = $config;
        $this->path           = $pathResolver;
        $this->commandFactory = $commandFactory;
        $this->session        = $session;
    }

    /**
     * Match the routing rules and run its corresponding controller
     *
     * @param string $pathOrDomain
     */
    protected function route($pathOrDomain)
    {
        $controller = $this->router->match($pathOrDomain);

        if ($controller instanceof Controller) {
            $controller->run($this->router->getMatchParams());
        }
    }

    /**
     * @param int             $errCode
     * @param \Exception|null $exception
     */
    protected function error($errCode, $exception = null)
    {
        if ($exception instanceof \Exception) {
            $this->view->setContent($exception->getMessage() . "\n");
        }

        $this->respond($errCode);
    }

    /**
     * Send the response with output from the view
     *
     * @param int $statusCode
     */
    protected function respond($statusCode = null)
    {
        if (is_null($statusCode)) {
            $statusCode = Status::OK;
        }

        $status  = new Status($statusCode);
        $content = $this->view->toString();

        if (empty($content)) {
            $content = $status->getStatusText() . "\n";
        }

        $this->response->respond($status, $content);
    }

    /**
     * Handle an error
     *
     * @param \Exception $e
     */
    public function err(\Exception $e)
    {
        echo "<pre>\n";

        echo $e->getMessage() . "\n";

        if ($this->config->get('debug.print_backtrace')) {
            echo $e->getTraceAsString() . "\n";
        }

        echo "</pre>\n";
    }
}
