<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Entity\Quantity;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
        $total = $this->cartUpdate($request);

        /**
         * @var ArrayCollection|Product[]
         */
        $cart = $this->getUser()->getCart()->getProducts();
        $previousUrl = $request->headers->get('referer');

        if ($total != 0) {
            return $this->render("cart/showUpdated.html.twig", [
                'cart' => $cart,
                'previousUrl' => $previousUrl,
                'total' => $total
            ]);
        }

        return $this->render("cart/show.html.twig", [
            'cart' => $cart,
            'previousUrl' => $previousUrl,
            'total' => 0
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
        $cart = $user->getCart();
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);
        $quantity = new Quantity();
        $quantity->setProduct($product);
        $cart->addQuantity($quantity);
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute("show_cart");
    }

    /**
     * @Route("/cart/remove/{id}", name="remove_from_cart")
     * @param $id
     * @return Response
     */
    public function removeFromCart($id)
    {
        $em = $this->getDoctrine()->getManager();
        $quantity = $em->getRepository(Quantity::class)->findOneBy(["product" => $id]);
        if ($quantity != null) {
            $user = $this->getUser();
            $cart = $user->getCart();
            $cart->removeQuantity($quantity);;
            $em->persist($user);
            $em->flush();
        }
        return $this->redirectToRoute("show_cart");
    }

    public function cartUpdate(Request $request)
    {
        $query = $request->query;
        $products = $this->getUser()->getCart()->getProducts();
        $em = $this->getDoctrine()->getManager();
        $total = 0;
        foreach ($products as $product) {
            $quantityForProduct = $query->get($product->getId());
            $quantity = $em->getRepository(Quantity::class)->findOneBy(["product" => $product->getId()]);
            $quantity->setQuantity($quantityForProduct);
            $em->persist($quantity);
            $em->flush();
            $total += $quantityForProduct * $product->getPrice();
        }
        return $total;
    }

    public function removeAll()
    {

    }

    /**
     * @Route("cart/checkout/", name="cart_checkout")
     */
    public function checkOut(Request $request)
    {
        $productsInTheCart = $this->getUser()->getCart()->getProducts();
        $em = $this->getDoctrine()->getManager();
        $bla = $request->query;

        foreach ($productsInTheCart as $product) {
            $product->buy($this->getUser());
            $em->persist($this->getUser());
            $em->persist($product->getAuthor());
            $em->flush();
        }

        $this->getUser()->emptyCart();
        $em->persist($this->getUser());
        $em->flush();
        return $this->redirectToRoute("homepage");
    }

}
