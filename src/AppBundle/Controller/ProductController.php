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
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
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

            return $this->redirectToRoute('homepage');
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
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
            'products' => $products
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
        $product->setIsValid(false);
        $em->persist($product);
        $em->flush();
        return $this->redirectToRoute("show_all_products");
    }
}
