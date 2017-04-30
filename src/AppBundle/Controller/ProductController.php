<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Form\ProductType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ProductController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/product/add", name="product_create")
     * @Security("has_role('ROLE_EDITOR')")
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Category::class)->findAll();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var User
             */
            $user = $this->getUser();
            $product->setAuthor($user);
            $user->addCreatedProduct($product);

            /**
             * @var UploadedFile $file
             */
            $file = $product->getPicture();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('product_pictures'),
                $fileName
            );

            $product->setPicture($fileName);
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('show_all_products');
        }
        $previousUrl = $request->headers->get('referer');
        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'previousUrl' => $previousUrl,
        ]);
    }

    /**
     * @Route("/profile/products", name="show_my_products")
     */
    public function showMyProducts()
    {
        $token = $this->get('security.csrf.token_manager')->getToken('asd');
        /**
         * @var ArrayCollection|Product
         */
        $products = $this->getUser()->getProducts();
        return $this->render('product/showMyProducts.html.twig', array(
            'products' => $products,
            'token' => $token
        ));
    }

    /**
     * @Route("/products", name="show_all_products")
     */
    public function showAllProducts()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository(Product::class)->findAll();
        return $this->render("product/showAll.html.twig", [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/categories", name="show_by_category")
     * @param Request $request
     * @return Response
     */
    public function showByCategory(Request $request)
    {
        $category = $request->query->get("category");
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Category::class)->findAll();
        if ($category != null) {
            $products = $em->getRepository(Product::class)->findBy(["category" => $category]);
            $count = count($products);
            $categoryName = $em->getRepository(Category::class)->find($category);
            return $this->render("product/showAll.html.twig", [
                "products" => $products,
                "count" => $count,
                "category" => $categoryName
            ]);
        }

        return $this->render("product/showByCategory.html.twig", [
            "categories" => $categories
        ]);
    }

    /**
     * @Route("/product/sale/{id}/{token}", name="put_for_sale")
     * @param $id
     * @param $token
     * @return Response
     */
    public function putForSale($id, $token)
    {
        if (!$this->isCsrfTokenValid('asd', $token)) {
            return $this->redirectToRoute("homepage");
        }
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);
        if (!$this->getUser()->isAuthor($product)) {
            return $this->redirectToRoute("show_all_products");
        }
        $product->setIsValid(true);
        $em->persist($product);
        $em->flush();
        return $this->redirectToRoute("show_all_products");
    }

    /**
     * @Route("/product/stop/{id}", name="stop_from_sell")
     * @param $id
     * @return Response
     */
    public function stopFromSell($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);
        if (!$this->getUser()->isAuthor($product)) {
            return $this->redirectToRoute("show_all_products");
        }
        $product->setIsValid(false);
        $em->persist($product);
        $em->flush();
        return $this->redirectToRoute("show_all_products");
    }
}
