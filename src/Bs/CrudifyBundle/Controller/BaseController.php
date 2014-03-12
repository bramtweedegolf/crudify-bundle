<?php

namespace Bs\CrudifyBundle\Controller;

use Bs\CrudifyBundle\Definition\DefinitionInterface;
use Bs\CrudifyBundle\Event\CrudifyEvents;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends AbstractCrudController
{
    /**
     * {@inheritdoc}
     */
    public function indexAction(DefinitionInterface $definition, Request $request)
    {
        $query = $this->createSelectQuery($definition->getIndex());
        $grid = $this->getGrid($query, $definition, $request);

        return $this->render($definition->getIndexTemplate(), [
            'definition' => $definition,
            'objects' => $grid,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function newAction(DefinitionInterface $definition, Request $request)
    {
        $form = $this->createCreateForm($definition);
        $object = $form->getData();

        return $this->render($definition->getNewTemplate(), [
            'definition' => $definition,
            'form' => $form->createView(),
            'object' => $object,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function createAction(DefinitionInterface $definition, Request $request)
    {
        $form = $this->createCreateForm($definition);
        $form->handleRequest($request);
        $object = $form->getData();

        if ($form->isValid()) {
            $this->triggerEvent(CrudifyEvents::BEFORE_CREATE, $object, $definition);
            $manager = $definition->getEntityManager();
            $manager->persist($object);
            $manager->flush();
            $this->triggerEvent(CrudifyEvents::CREATE, $object, $definition);
            return $this->determineSuccessResponse($definition, $object, $request);
        }

        return $this->render($definition->getNewTemplate(), [
            'definition' => $definition,
            'form' => $form->createView(),
            'object' => $object,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function editAction(DefinitionInterface $definition, Request $request, $id)
    {
        $object = $this->retrieveObject($definition, $id);
        if (null === $object) {
            throw $this->createNotFoundException();
        }

        $form = $this->createUpdateForm($definition, $object);
        return $this->render($definition->getEditTemplate(), [
            'definition' => $definition,
            'form' => $form->createView(),
            'object' => $object,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function updateAction(DefinitionInterface $definition, Request $request, $id)
    {
        $object = $this->retrieveObject($definition, $id);
        if (null === $object) {
            throw $this->createNotFoundException();
        }

        $form = $this->createUpdateForm($definition, $object);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->triggerEvent(CrudifyEvents::BEFORE_UPDATE, $object, $definition);
            $manager = $definition->getEntityManager();
            $manager->flush();
            $this->triggerEvent(CrudifyEvents::UPDATE, $object, $definition);
            return $this->determineSuccessResponse($definition, $object, $request);
        }
        return $this->render($definition->getEditTemplate(), [
            'definition' => $definition,
            'form' => $form->createView(),
            'object' => $object,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAction(DefinitionInterface $definition, Request $request, $id)
    {
        $object = $this->retrieveObject($definition, $id);
        if (null === $object) {
            throw $this->createNotFoundException();
        }

        $form = $this->createDeleteForm($definition, $object);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->triggerEvent(CrudifyEvents::BEFORE_DELETE, $object, $definition);
            $manager = $definition->getEntityManager();
            $manager->remove($object);
            $manager->flush();
            $this->triggerEvent(CrudifyEvents::DELETE, $object, $definition);
        }

        return $this->redirect($this->generateUrl('bs_crudify.index', [
            'mapping' => $definition->getName(),
        ]));
    }
}