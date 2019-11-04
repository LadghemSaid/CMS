<?php

namespace PiedWeb\CMSBundle\TemplateEditor;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;

class ElementAdmin extends AbstractController
{
    protected function getElements()
    {
        return new ElementRepository($this->get('kernel')->getProjectDir().'/templates');
    }

    public function listElement()
    {
        return $this->render('@PiedWebCMS/admin/theme.list.html.twig', ['elements' => $this->getElements()->getAll()]);
    }

    protected function getElement($encodedPath)
    {
        if (null !== $encodedPath) {
            $element = $this->getElements()->getOneByEncodedPath($encodedPath);
            if (!$element) {
                throw $this->createNotFoundException('This element does not exist...');
            }
        }

        return $element ?? new Element($this->get('kernel')->getProjectDir().'/templates');
    }

    public function editElement($encodedPath = null, Request $request = null)
    {
        $element = $this->getElement($encodedPath);

        $form = $this->editElementForm($element);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $element = $form->getData();
            $element->storeElement();

            return $this->redirectToRoute('piedweb_cms_template_editor_edit', [
                'encodedPath' => $element->getEncodedPath(),
            ]);
        }

        return $this->render('@PiedWebCMS/admin/theme.edit.html.twig', [
            'element' => $element,
            'form' => $form->createView(),
        ]);
    }

    protected function editElementForm(Element $element)
    {
        return $this->createFormBuilder($element)
            ->add('path', TextType::class)
            ->add('code', TextareaType::class, [
                'attr' => [
                    'style' => 'min-height: 90vh;font-size:125%;',
                    'data-editor' => 'markdown',
                    'data-gutter' => 0,
                ],
            ])

            ->getForm();
    }

    public function deleteElement($encodedPath, Request $request)
    {
        $element = $this->getElement($encodedPath);

        $form = $this->createFormBuilder()
           ->add('delete', SubmitType::class, ['label' => 'Supprimer', 'attr' => ['class' => 'btn-danger']])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $element->deleteElement();

            $this->addFlash('error', 'Element supprimé.');

            return $this->redirectToRoute('piedweb_cms_template_editor_list');
        }

        return $this->render('@PiedWebCMS/admin/theme.delete.html.twig', [
            'form' => $form->createView(),
            'element' => $element,
        ]);
    }
}