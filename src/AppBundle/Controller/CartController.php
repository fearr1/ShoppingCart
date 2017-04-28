<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    /**
     * @Route("/cart", name="show_cart")
     * @param Request $request
     * @return Response
     */
    public function showCart(Request $request)
    {
        /**
         * @var ArrayCollection|Product[]
         */
        $cart = $this->getUser()->getCart();
        $previousUrl = $request->headers->get('referer');
        return $this->render("cart/show.html.twig", [
            'cart' => $cart,
            'previousUrl' => $previousUrl
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
