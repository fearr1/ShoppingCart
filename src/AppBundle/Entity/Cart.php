<?php
/**
 * Created by PhpStorm.
 * User: xeroe
 * Date: 28.4.2017 г.
 * Time: 19:10
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="carts")
 */
class Cart
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Quantity", mappedBy="cart", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $quantities;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", mappedBy="cart" )
     */
    private $user;

    public function __construct()
    {
        $this->quantities = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getQuantities()
    {
        return $this->quantities->toArray();
    }

    public function addQuantity(Quantity $quantity)
    {
        if (!$this->quantities->contains($quantity)) {
            $this->quantities->add($quantity);
            $quantity->setCart($this);
        }

        return $this;
    }

    public function removeQuantity(Quantity $quantity)
    {
        if ($this->quantities->contains($quantity)) {
            $this->quantities->removeElement($quantity);
            $quantity->setCart(null);
        }

        return $this;
    }

    public function getProducts()
    {
        return array_map(
            function ($quantity) {
                return $quantity->getProduct();
            },
            $this->quantities->toArray()
        );
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getProductsCount()
    {
        return count($this->getQuantities());
    }

    public function isInTheCart(Product $product)
    {
        $products = $this->getProducts();
        return in_array($product, $products);
    }

}