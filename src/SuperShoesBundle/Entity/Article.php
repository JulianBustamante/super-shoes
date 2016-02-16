<?php

namespace SuperShoesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="SuperShoesBundle\Repository\ArticleRepository")
 */
class Article
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
     * @Assert\NotBlank()
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var float
     *
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="total_in_shelf", type="integer")
     */
    private $totalInShelf;

    /**
     * @var int
     *
     * @ORM\Column(name="total_in_vault", type="integer")
     */
    private $totalInVault;


    /**
     * @ORM\ManyToOne(targetEntity="Store")
     */
    private $store;


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
     * @return Article
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
     * Set description
     *
     * @param string $description
     *
     * @return Article
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
     * Set price
     *
     * @param string $price
     *
     * @return Article
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set totalInShelf
     *
     * @param integer $totalInShelf
     *
     * @return Article
     */
    public function setTotalInShelf($totalInShelf)
    {
        $this->totalInShelf = $totalInShelf;

        return $this;
    }

    /**
     * Get totalInShelf
     *
     * @return int
     */
    public function getTotalInShelf()
    {
        return $this->totalInShelf;
    }

    /**
     * Set totalInVault
     *
     * @param integer $totalInVault
     *
     * @return Article
     */
    public function setTotalInVault($totalInVault)
    {
        $this->totalInVault = $totalInVault;

        return $this;
    }

    /**
     * Get totalInVault
     *
     * @return int
     */
    public function getTotalInVault()
    {
        return $this->totalInVault;
    }

    /**
     * Set store
     *
     * @param Store $store
     *
     * @return Store
     */
    public function setStore(Store $store)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Get storeId
     *
     * @return int
     */
    public function getStore()
    {
        return $this->store;
    }
}
