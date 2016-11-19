<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bas_user")
 */
class User
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $user_id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $user_name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $parents;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $children;

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set userName
     *
     * @param string $userName
     *
     * @return User
     */
    public function setUserName($userName)
    {
        $this->user_name = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * Set parents
     *
     * @param string $parents
     *
     * @return User
     */
    public function setParents($parents)
    {
        $this->parents = $parents;

        return $this;
    }

    /**
     * Get parents
     *
     * @return string
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * Set children
     *
     * @param string $children
     *
     * @return User
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get children
     *
     * @return string
     */
    public function getChildren()
    {
        return $this->children;
    }
}
