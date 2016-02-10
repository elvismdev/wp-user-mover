<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WpWumCount
 *
 * @ORM\Table(name="wp_wum_count")
 * @ORM\Entity
 */
class WpWumCount
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="moved", type="integer", nullable=false)
     */
    private $moved;

    /**
     * @var integer
     *
     * @ORM\Column(name="exist", type="integer", nullable=false)
     */
    private $exist;

    /**
     * @var integer
     *
     * @ORM\Column(name="ignored", type="integer", nullable=false)
     */
    private $ignored;

    /**
     * @var integer
     *
     * @ORM\Column(name="error", type="integer", nullable=false)
     */
    private $error;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->moved = 0;
        $this->exist = 0;
        $this->ignored = 0;
        $this->error = 0;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getMoved()
    {
        return $this->moved;
    }

    /**
     * @return $this
     */
    public function setMoved()
    {
        $this->moved++;
        return $this;
    }

    /**
     * @return int
     */
    public function getExist()
    {
        return $this->exist;
    }

    /**
     * @return $this
     */
    public function setExist()
    {
        $this->exist++;
        return $this;
    }

    /**
     * @return int
     */
    public function getIgnored()
    {
        return $this->ignored;
    }

    /**
     * @return $this
     */
    public function setIgnored()
    {
        $this->ignored++;
        return $this;
    }

    /**
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return $this
     */
    public function setError()
    {
        $this->error++;
        return $this;
    }

    /**
     * @return $this
     */
    public function setClear()
    {
        $this->__construct();
        return $this;
    }

    public function toArray()
    {
        return array(
            'moved' => $this->moved,
            'exist' => $this->exist,
            'ignored' => $this->ignored,
            'errors' => $this->error
        );
    }
}
