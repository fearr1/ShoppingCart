<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    /**
     * @Route("/cart", name="show_cart")
     */
    public function showCart()
    {
        $cart = $this->getUser()->getCart();
        return $this->render("cart/show.html.twig", [
            'cart' => $cart
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     * @param $id
     * @return Response
     */
    public function addToCart($id)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);
        $user->addToCart($product);
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute("homepage");
    }

    /**
     * @Route("/cart/remove/{id}", name="remove_from_cart")
     * @param $id
     * @return Response
     */
    public function removeFromCart($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);
        if($product != null) {
            $user = $this->getUser();
            $user->removeFromCart($product);
            $em->persist($user);
            $em->flush();
        }
        return $this->redirectToRoute("show_cart");
    }

    public function removeAll()
    {

    }

    public function buy()
    {

    }

}
