<?php
/**
 * Created by PhpStorm.
 * User: xeroe
 * Date: 28.4.2017 г.
 * Time: 19:27
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="quantities")
 */
class Quantity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Cart", inversedBy="quantities")
     * @ORM\JoinColumn(name="cart_id", referencedColumnName="id", nullable=FALSE)
     */
    private $cart;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product", inversedBy="quantities")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=FALSE)
     */
    private $product;

    /**
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    public function getId()
    {
        return $this->id;
    }

    public function getCart()
    {
        return $this->cart;
    }

    public function setCart(Cart $cart = null)
    {
        $this->cart = $cart;
        return $this;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct(Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function __construct()
    {
        $this->quantity = 1;
    }

}