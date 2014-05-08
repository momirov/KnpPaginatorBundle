<?php

namespace Knp\Bundle\PaginatorBundle\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;
use Knp\Component\Pager\Event\AfterEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $request The request object
     * @param array                                                     $params  The keys of the fields from the Paginator options to synchronize
     */
    public function __construct(RequestStack $requestStack, $params = array())
    {
        $this->requestStack = $requestStack;
        $this->params = $params;
    }

    /**
     * Updates the sorting parameter in $_GET to match the Request object
     */
    public function items(ItemsEvent $event)
    {
        foreach ($this->params as $option) {
            if (isset($event->options[$option])) {
                $name = $event->options[$option];

                if (null !== $this->requestStack->getCurrentRequest()->get($name)
                    && (!array_key_exists($name, $_GET) || $_GET[$name] !== $this->requestStack->getCurrentRequest()->get($name))
                ) {
                    $_GET[$name] = $this->requestStack->getCurrentRequest()->get($name);
                }
            }
        }
    }

    /**
     * Unset $_GET variables
     */
    public function after(AfterEvent $event)
    {
        $options = $event->getPaginationView()->getPaginatorOptions();
        foreach ($this->params as $name) {
            unset($_GET[$options[$name]]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'knp_pager.items' => array('items', 2),
            'knp_pager.after' => array('after', 1)
        );
    }
}
