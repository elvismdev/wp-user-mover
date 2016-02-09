<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WpUsers
 *
 * @ORM\Table(name="wp_users", indexes={@ORM\Index(name="user_login_key", columns={"user_login"}), @ORM\Index(name="user_nicename", columns={"user_nicename"})})
 * @ORM\Entity
 */
class WpUsers
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="user_login", type="string", length=60, nullable=false)
     */
    private $userLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="user_pass", type="string", length=255, nullable=false)
     */
    private $userPass;

    /**
     * @var string
     *
     * @ORM\Column(name="user_nicename", type="string", length=50, nullable=false)
     */
    private $userNicename;

    /**
     * @var string
     *
     * @ORM\Column(name="user_email", type="string", length=100, nullable=false)
     */
    private $userEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="user_url", type="string", length=100, nullable=false)
     */
    private $userUrl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="user_registered", type="datetime", nullable=false)
     */
    private $userRegistered;

    /**
     * @var string
     *
     * @ORM\Column(name="user_activation_key", type="string", length=255, nullable=false)
     */
    private $userActivationKey;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_status", type="integer", nullable=false)
     */
    private $userStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=250, nullable=false)
     */
    private $displayName;

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
    public function getUserLogin()
    {
        return $this->userLogin;
    }

    /**
     * @param string $userLogin
     * @return $this
     */
    public function setUserLogin($userLogin)
    {
        $this->userLogin = $userLogin;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserPass()
    {
        return $this->userPass;
    }

    /**
     * @param string $userPass
     * @return $this
     */
    public function setUserPass($userPass)
    {
        $this->userPass = $userPass;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserNicename()
    {
        return $this->userNicename;
    }

    /**
     * @param string $userNicename
     * @return $this
     */
    public function setUserNicename($userNicename)
    {
        $this->userNicename = $userNicename;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * @param string $userEmail
     * @return $this
     */
    public function setUserEmail($userEmail)
    {
        $this->userEmail = $userEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserUrl()
    {
        return $this->userUrl;
    }

    /**
     * @param string $userUrl
     * @return $this
     */
    public function setUserUrl($userUrl)
    {
        $this->userUrl = $userUrl;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUserRegistered()
    {
        return $this->userRegistered;
    }

    /**
     * @param \DateTime $userRegistered
     * @return $this
     */
    public function setUserRegistered($userRegistered)
    {
        $this->userRegistered = $userRegistered;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserActivationKey()
    {
        return $this->userActivationKey;
    }

    /**
     * @param string $userActivationKey
     * @return $this
     */
    public function setUserActivationKey($userActivationKey)
    {
        $this->userActivationKey = $userActivationKey;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserStatus()
    {
        return $this->userStatus;
    }

    /**
     * @param int $userStatus
     * @return $this
     */
    public function setUserStatus($userStatus)
    {
        $this->userStatus = $userStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return $this
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }
}
