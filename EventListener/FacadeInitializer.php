<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\EventListener;

use WouterJ\EloquentBundle\Facade\Facade;
use WouterJ\EloquentBundle\Facade\AliasesLoader;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Initializes the facades.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class FacadeInitializer implements EventSubscriberInterface
{
    /** @var null|AliasesLoader */
    private $loader;
    /** @var Container */
    private $container;
    private $run = false;

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'initialize',
            ConsoleEvents::COMMAND => 'initializeConsole',
        );
    }

    /**
     * @param Container $container
     */
    public function __construct($container)
    {
        $this->setContainer($container);
    }

    /**
     * Configures the facades and registers the aliases loader, when
     * activated.
     */
    public function initialize()
    {
        Facade::setContainer($this->getContainer());

        if (null !== $loader = $this->getLoader()) {
            $loader->register();
        }
    }

    public function initializeConsole()
    {
        if ($this->run) {
            return;
        }

        $this->initialize();
        $this->run = true;
    }

    protected function getLoader()
    {
        return $this->loader;
    }

    public function setLoader(AliasesLoader $loader)
    {
        $this->loader = $loader;
    }

    protected function getContainer()
    {
        return $this->container;
    }

    private function setContainer(Container $container)
    {
        $this->container = $container;
    }
}
