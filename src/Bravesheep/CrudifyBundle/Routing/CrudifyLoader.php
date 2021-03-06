<?php

namespace Bravesheep\CrudifyBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class CrudifyLoader extends Loader
{
    private $defaultMapping;

    public function __construct($defaultMapping = null)
    {
        $this->defaultMapping = $defaultMapping;
    }

    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();

        if ($this->defaultMapping !== null) {
            $route = new Route('/', [
                '_controller' => 'FrameworkBundle:Redirect:redirect',
                'route' => 'bravesheep_crudify.index',
                'permanent' => true,
                'mapping' => $this->defaultMapping,
            ]);
            $routes->add('bravesheep_crudify.homepage_redirect', $route);
        }


        $mappingReq = ['mapping' => '\w+'];
        $mappingIdReq = array_merge($mappingReq, ['id' => '\d+']);

        $routes->add(
            'bravesheep_crudify.index',
            $this->createRoute('/{mapping}', 'indexAction', $mappingReq, ['GET'])
        );
        $routes->add(
            'bravesheep_crudify.new',
            $this->createRoute('/{mapping}/new', 'newAction', $mappingReq, ['GET'])
        );
        $routes->add(
            'bravesheep_crudify.create',
            $this->createRoute('/{mapping}', 'createAction', $mappingReq, ['POST'])
        );
        $routes->add(
            'bravesheep_crudify.edit',
            $this->createRoute('/{mapping}/{id}', 'editAction', $mappingIdReq, ['GET'])
        );
        $routes->add(
            'bravesheep_crudify.update',
            $this->createRoute('/{mapping}/{id}', 'updateAction', $mappingIdReq, ['PUT', 'PATCH'])
        );
        $routes->add(
            'bravesheep_crudify.delete',
            $this->createRoute('/{mapping}/{id}', 'deleteAction', $mappingIdReq, ['DELETE'])
        );
        return $routes;
    }

    private function createRoute($path, $action, array $requirements, array $methods)
    {
        $route = new Route(
            $path,
            ['_controller' => "bravesheep_crudify.controller:{$action}"],
            $requirements,
            [],
            '',
            [],
            $methods
        );
        return $route;
    }

    public function supports($resource, $type = null)
    {
        return $type === 'crudify';
    }
}
