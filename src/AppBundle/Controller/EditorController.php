<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Form\CategoryType;
use AppBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EditorController
 * @package AppBundle\Controller
 * @Security("has_role('ROLE_EDITOR')")
 * @Route("/editor")
 */
class EditorController extends Controller
{
    /**
     * @Route("/category/create", name="category_create")
     * @param Request $request
     * @return Response
     */
    public function createCategory(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute("show_all_products");
        }
        return $this->render("editor/category/create.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/category/delete", name="delete_category")
     * @param Request $request
     * @return Response
     */
    public function deleteCategory(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $request->query;
        $categoryId = $query->get("category");
        if ($categoryId != null) {
            $productsWithCategory = $em->getRepository(Product::class)->findBy(["category" => $categoryId]);
            $otherCategory = $em->getRepository(Category::class)->findOneBy(["name" => "other"]);
            foreach ($productsWithCategory as $product) {
                $product->setCategory($otherCategory);
                $em->persist($product);
                $em->flush();
            }
            $categoryToDelete = $em->getRepository(Category::class)->find($categoryId);
            $em->remove($categoryToDelete);
            $em->flush();

            return $this->redirectToRoute("homepage");
        }
        $categories = $em->getRepository(Category::class)->findAll();
        return $this->render("editor/category/delete.html.twig", [
            "categories" => $categories
        ]);


    }

    /**
     * @Route("/product/edit/{id}", name="product_edit")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editProduct(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Category::class)->findAll();
        $product = $em->getRepository(Product::class)->find($id);
        $previousUrl = $request->query->get("referer");
        if($product === null){
            return $this->redirectToRoute("show_all_products");
        }
        $oldPicture = $product->getPicture();
        $product->setPicture(null);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            /**
             * @var UploadedFile $file
             */
            $file = $product->getPicture();
            if($file != null) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('product_pictures'),
                    $fileName
                );
            }else {
                $fileName = $oldPicture;
            }

            $product->setPicture($fileName);
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute("show_all_products");
        }

        return $this->render("editor/product/edit.html.twig", [
           "form" => $form->createView(),
            "product" => $product,
            "categories" => $categories,
            "previousUrl" => $previousUrl,
            ]);
    }

    /**
     * @Route("/product/delete/{id}", name="product_delete")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function deleteProduct(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Category::class)->findAll();
        $product = $em->getRepository(Product::class)->find($id);
        $previousUrl = $request->query->get("referer");
        $confirm = $request->query->get("confirm");
        if($confirm == "yes"){
            $em->remove($product);
            $em->flush();

            return $this->redirectToRoute("show_all_products");
        }

        return $this->render("editor/product/delete.html.twig", [
            "product" => $product,
            "categories" => $categories,
            "previousUrl" => $previousUrl
        ]);

    }

}
