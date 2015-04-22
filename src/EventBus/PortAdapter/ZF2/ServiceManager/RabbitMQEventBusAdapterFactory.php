<?php
namespace EventBus\PortAdapter\ZF2\ServiceManager;


use EventBus\PortAdapter\RabbitMQ\EventBusAdapter;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RabbitMQEventBusAdapterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return EventBusAdapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
	    $config = $serviceLocator->get('Config');
	    /** @var \AMQPExchange $exchange */
        $exchange = $serviceLocator->get('amqp.exchanges.messageBus');
        $queue = new \AMQPQueue($exchange->getChannel());
        if(empty($config['amqp']['boundedContext'])) {
	        throw new ServiceNotCreatedException('Please specify boundedContext in amqp config section. It should be application-dependable.');
        }
        $queue->setName($config['amqp']['boundedContext']);
        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();

        return new EventBusAdapter($queue, $exchange);
    }


}