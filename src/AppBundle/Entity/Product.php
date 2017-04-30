<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\Length(max="30",
     *     min="5",
     *     minMessage="Name must be at least 5 chars",
     *     maxMessage="The limit of chars for name is 30")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     * @Assert\Range(min="1",
     *     minMessage="Minimum price for product is $1")
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\Length(min="15",
     *     minMessage="Minimum length for description is 15 chars",
     *     max="100",
     *     maxMessage="Maximum length for description is 100 chars")
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     * @Assert\Range(min= 1,
     *     minMessage="Minimum count for quantity is 1",
     *     max = 100,
     *     maxMessage="Maximum count for quantity is 100")
     */
    private $quantity;

    /**
     * @var int
     *
     * @@ORM\Column(name="author_id", type="integer")
     */
    private $authorId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="products")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_valid", type="boolean")
     */
    private $valid;

    /**
     * @ORM\Column(name="picture", type="string")
     * @Assert\Image()
     */
    private $picture;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     */
    private $category;

    /**
     * @var int
     *
     * @@ORM\Column(name="category_id", type="integer", nullable=true)
     */
    private $categoryId;

    /**
     * @ORM\OneToMany(targetEntity="Quantity", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $quantities;

    /**
     * @param Category $category
     *
     * @return Product
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Category|string
     */
    public function getCategory()
    {
        if ($this->category == null) {
            return "other";
        }
        return $this->category;
    }

    /**
     * @param int $categoryId
     *
     * @return Product
     */
    public function setCategoryId(int $categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @param User $author
     *
     * @return Product
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param int $authorId
     *
     * @return Product
     */
    public function setAuthorId(int $authorId)
    {
        $this->authorId = $authorId;
        return $this;
    }

    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @param bool $isValid
     */
    public function setIsValid(bool $isValid)
    {
        $this->valid = $isValid;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    public function __construct()
    {
        $this->setIsValid(true);
        $this->quantities = new ArrayCollection();
    }

    public function getQuantities()
    {
        return $this->quantities->toArray();
    }

    public function addQuantity(Quantity $quantity)
    {
        if (!$this->quantities->contains($quantity)) {
            $this->quantities->add($quantity);
            $quantity->setProduct($this);
        }

        return $this;
    }

    public function removeQuantity(Quantity $quantity)
    {
        if ($this->quantities->contains($quantity)) {
            $this->quantities->removeElement($quantity);
            $quantity->setProduct(null);
        }

        return $this;
    }

    public function getRequestedQuantity()
    {
        return $this->getQuantities()[0]->getQuantity();
    }

    public function reduceQuantity($quantity)
    {
        $this->quantity -= $quantity;
    }

    public function addQuantityToProduct($quantity)
    {
        $this->quantity += $quantity;
    }


    public function buy(User $user)
    {
        $user->reduceCash($this->getPrice());
        $authorProduct = $this->getAuthor();
        $authorProduct->addCash($this->getPrice());
        $product = null;

        $this->reduceQuantity($this->getRequestedQuantity());
        if ($user->isInTheProducts($this)) {
            $product = $user->isInTheProducts($this);
            $product->addQuantityToProduct($this->getRequestedQuantity());
        } else {
            $product = new Product();
            $product->setName($this->getName());
            $product->setAuthor($user);
            $product->setCategory($this->getCategory());
            $product->setPrice($this->getPrice());
            $product->setDescription($this->getDescription());
            $product->setQuantity($this->getRequestedQuantity());
            $product->setIsValid(false);
            $product->setPicture($this->getPicture());
            $user->addCreatedProduct($product);
        }
        if ($this->getQuantity() <= 0) {
            $this->setIsValid(false);
        }
    }

}

