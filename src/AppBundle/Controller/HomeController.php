<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @param Request $request
     * @Route("/", name="homepage")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return $this->redirectToRoute("show_all_products");
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/search", name="search_action")
     */
    public function searchAction(Request $request)
    {
        $search = $request->query->get("name");
        $productsToShow = [];
        $search = strtolower($search);
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository(Product::class)->findBy(["valid" => true]);
        foreach ($products as $product) {
            $productName = strtolower($product->getName());
            if (strpos($productName, $search) !== false) {
                $productsToShow[] = $product;
            }
        }
        $count = count($productsToShow);

        return $this->render("product/showBySearch.html.twig", [
            "products" => $productsToShow,
            "search" => $search,
            "count" => $count
        ]);
    }

    /**
     * @Route("/search/category", name="search_by_category")
     * @Method({"GET"})
     * @param Request $request
     * @return Response
     */
    public function searchByCategory(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var ArrayCollection|Category[]
         */
        $categories = $em->getRepository(Category::class)->findAll();
        $products = $em->getRepository(Product::class)->findBy(["valid" => true]);
        $productsToShow = [];
        $categoryId = $request->query->get("category_id");
        $search = strtolower($request->query->get("name"));
        if ($categoryId != null && $search != null) {
            foreach ($products as $product) {
                if (strpos(strtolower($product->getName()), $search) !== false) {
                    if ($product->getCategory()->getId() == $categoryId) {
                        $productsToShow[] = $product;
                    }
                }
            }
            $count = count($productsToShow);
            $category = $em->getRepository(Category::class)->find($categoryId);
            return $this->render("/product/showByCategorySearch.html.twig", [
                "products" => $productsToShow,
                "search" => $search,
                "count" => $count,
                "category" => $category
            ]);

        }
        $previousUrl = $request->headers->get('referer');
        return $this->render("default/searchByCategory.html.twig", [
            "categories" => $categories,
            "previousUrl" => $previousUrl,
        ]);
    }
}
