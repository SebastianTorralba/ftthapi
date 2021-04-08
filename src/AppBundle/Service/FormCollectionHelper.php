<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

/**
 * Rutinas de código reutilizables cuando se utilizan 
 * colecciones dinámicas en los formularios.
 * 
 * Modo de uso:
 * 
 *      if ($request->isMethod('POST')) {
 *          $object = $form->getData();
 *          $formCollectionHelper->init($object, 'getRelacionados');
 *          $form->bind($request);
 *          if ($form->isValid()) {
 *              $formCollectionHelper->update($object, 'getRelacionados');
 *              $entityManager->pesist($object);
 *              $entityManager->flush();
 *          }
 *      }
 */
class FormCollectionHelper
{
    /**
     * @var EntityManager
     */
    protected $em;
    
    protected $collections = array();
    
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }
    
    public function init($object, $methodsGetCollection)
    {
        if (!is_array($methodsGetCollection)) {
            $methodsGetCollection = array($methodsGetCollection);
        }
        
        foreach ($methodsGetCollection as $methodGetCollection) {
            $this->collections[$methodGetCollection] = array();
            foreach($object->$methodGetCollection() as $rel) {
                $this->collections[$methodGetCollection][] = $rel;               
            }        
        }
    }
    
    public function update($object, $methodsGetCollection)
    {
        if (!is_array($methodsGetCollection)) {
            $methodsGetCollection = array($methodsGetCollection);
        }
        
        foreach ($methodsGetCollection as $methodGetCollection) {        
            foreach ($object->$methodGetCollection() as $rel) {
                foreach ($this->collections[$methodGetCollection] as $k => $toDel) {
                    if ($toDel->getId() === $rel->getId()) {
                        unset($this->collections[$methodGetCollection][$k]);
                    }
                }
            }
            foreach ($this->collections[$methodGetCollection] as $rel) {
                $this->em->remove($rel);
            }
        }
    }
}
