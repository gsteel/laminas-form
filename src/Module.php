<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form;

class Module
{
    /**
     * Return laminas-form configuration for laminas-mvc application.
     *
     * @return array
     */
    public function getConfig()
    {
        $provider = new ConfigProvider();
        return [
            'service_manager' => $provider->getDependencyConfig(),
            'view_helpers'    => $provider->getViewHelperConfig(),
        ];
    }

    /**
     * Register a specification for the FormElementManager with the ServiceListener.
     *
     * @param \Laminas\ModuleManager\ModuleEvent
     * @return void
     */
    public function init($event)
    {
        $container = $event->getParam('ServiceManager');
        $serviceListener = $container->get('ServiceListener');

        $serviceListener->addServiceManager(
            'FormElementManager',
            'form_elements',
            'Laminas\ModuleManager\Feature\FormElementProviderInterface',
            'getFormElementConfig'
        );
    }
}
