<?php

namespace TimesheetBundle\Entity;

/**
 * AdminConfig
 */
class AdminConfig
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $parameter;

    /**
     * @var string
     */
    private $value;


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
     * Set parameter
     *
     * @param string $parameter
     *
     * @return AdminConfig
     */
    public function setParameter($parameter)
    {
        $this->parameter = $parameter;

        return $this;
    }

    /**
     * Get parameter
     *
     * @return string
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return AdminConfig
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}

