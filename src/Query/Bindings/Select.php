<?php

namespace Adldap\Query\Bindings;

class Select extends AbstractBinding
{
    /**
     * The select field.
     *
     * @var string
     */
    protected $field;

    /**
     * Constructor.
     *
     * @param string $field
     */
    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * Returns the selected field.
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Returns the selected field.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getField();
    }
}
