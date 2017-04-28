<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface
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
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @var int
     *
     * @ORM\Column(name="age", type="integer")
     */
    private $age;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var int
     *
     * @ORM\Column(name="cash", type="integer", nullable=true)
     */
    private $cash;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered_on", type="datetime")
     */
    private $registeredOn;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product", mappedBy="author")
     */
    private $products;

    /**
     * @var array
     *
     * @ORM\Column(name="cart", type="array")
     */
    private $cart;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="user_roles",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    private $roles;

    public function addRole(Role $role)
    {
        $this->roles[] = $role;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge(int $age)
    {
        $this->age = $age;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    /**
     * @return int
     */
    public function getCash()
    {
        return $this->cash;
    }

    /**
     * @param int $cash
     */
    public function setCash(int $cash)
    {
        $this->cash = $cash;
    }

    /**
     * @return \DateTime
     */
    public function getRegisteredOn()
    {
        return $this->registeredOn;
    }

    /**
     * @param \DateTime $registeredOn
     */
    public function setRegisteredOn(\DateTime $registeredOn)
    {
        $this->registeredOn = $registeredOn;
    }

    /**
     * @param Product $product
     */
    public function addToCart($product)
    {
        $this->cart[] = $product;
    }

    /**
     * @return array
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param Product $product
     */
    public function removeFromCart(Product $product)
    {
        $cart = $this->getCart();
        foreach($cart as $element)
        {
            if($element->getId() == $product->getId())
            {
                $product = $element;
                $key = array_search($product, $cart, true);
                unset($cart[$key]);
                break;
            }
        }

        $this->setCart($cart);
    }

    public function setCart(array $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return ["ROLE_USER"];
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function addCreatedProduct(Product $product)
    {
        $this->products[] = $product;
    }

    /**
     * @return ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function __construct()
    {
        $this->setRegisteredOn(new \DateTime("NOW"));
        $this->products = new ArrayCollection();
        $this->cart = [];
        $this->cash = 3000;
    }

    public function __toString()
    {
        return $this->getUsername();

    }

    public function getCountCart()
    {
        return count($this->getCart());
    }
}

