<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PropertyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

use function PHPUnit\Framework\returnSelf;

class AdminController extends AbstractController
{
    private $repository;
    private $em;

    public function __construct(PropertyRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin/", name="admin.index")
     */
    public function index()
    {
        $properties = $this->repository->findAll();
        return $this->render('admin/admindex.html.twig', compact('properties'));
    }

    /**
     * @route "/admin/create", name="admin.new")
     */
    public function new(Request $request)
    {
        $property = new Property(); 
        $form = $this->createForm(PropertyType::Class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($property);
            $this->em->flush();
            $this->addFlash('success','Bien créer avec succès');
            return $this->redirectToRoute('admin_index'); 
        }

        return $this->render('admin/new.html.twig', [
            'property' => $property,
            'form' => $form->createView()
            ]);
    
    }
    /**
     * @Route("/admin/{id}", name="admin.edit", methods={"GET", "POST"})
     * @param Property $property
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Property $property, Request $request)
    {
        $form = $this->createForm(PropertyType::Class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush(); 
            $this->addFlash('success','Bien modifié avec succès');
            return $this->redirectToRoute('admin_index'); 
        }
        return $this->render('admin/edit.html.twig', [
        'property' => $property,
        'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/admin/{id}", name="admin_delete", methods={"DELETE"})
     */

    public function delete(Property $property)
    {
        $this->em->remove($property);
        $this->em->flush();
        $this->addFlash('success','Bien supprimé avec succès');
        return $this->redirectToRoute('admin_index');
    }
}
