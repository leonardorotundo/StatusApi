<?php

namespace StatusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Status
 *
 * @ORM\Table(name="status")
 * @ORM\Entity(repositoryClass="StatusBundle\Repository\StatusRepository")
 * @Serializer\ExclusionPolicy("all")
 * @ORM\HasLifecycleCallbacks()
 * 
 */
class Status
{
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     *     
     * )
     */
    private $email;

    /**
     * @var string
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = 120,
     *      maxMessage = "Your status cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var DateTime
     * @Serializer\Expose
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;
    
    /**
     * @var string
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;
    
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
     * Set email
     *
     * @param string $email
     *
     * @return Status
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Status
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @param DateTime $createdAt
     * @ORM\PrePersist
     *
     * @return Status
     */
    public function setCreatedAt()
    {
        $datetime = new \DateTime();
        $this->createdAt = $datetime;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get Code
     * @return type
     */
    function getCode() {
        return $this->code;
    }

    /**
     * 
     * @param type $code
     * @ORM\PrePersist
     */
    function setCode($code) {
        $this->code = rand(1, 100000000);
    }
    
}

