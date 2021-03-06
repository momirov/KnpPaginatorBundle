<?php

namespace Knp\Bundle\PaginatorBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;

class KnpPaginatorExtension extends Extension
{
    /**
     * Build the extension services
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('paginator.xml');

        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $container->setParameter('knp_paginator.template.pagination', $config['template']['pagination']);
        $container->setParameter('knp_paginator.template.filtration', $config['template']['filtration']);
        $container->setParameter('knp_paginator.template.sortable', $config['template']['sortable']);
        $container->setParameter('knp_paginator.page_range', $config['page_range']);

        $options = array();
        if ($config['clear_request_params']['sort_param']) {
            $options[] = 'sortFieldParameterName';
        }
        if ($config['clear_request_params']['sort_direction_param']) {
            $options[] = 'sortDirectionParameterName';
        }
        if ($config['clear_request_params']['filter_field_param']) {
            $options[] = 'filterFieldParameterName';
        }
        if ($config['clear_request_params']['filter_value_param']) {
            $options[] = 'filterValueParameterName';
        }
        $container->setParameter('knp_paginator.clear_request_params', $options);

        $paginatorDef = $container->getDefinition('knp_paginator');
        $paginatorDef->addMethodCall('setDefaultPaginatorOptions', array(array(
            'pageParameterName' => $config['default_options']['page_name'],
            'sortFieldParameterName' => $config['default_options']['sort_field_name'],
            'sortDirectionParameterName' => $config['default_options']['sort_direction_name'],
            'filterFieldParameterName' => $config['default_options']['filter_field_name'],
            'filterValueParameterName' => $config['default_options']['filter_value_name'],
            'distinct' => $config['default_options']['distinct']
        )));
    }
}
