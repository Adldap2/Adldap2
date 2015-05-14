<?php

namespace Adldap\Query;

/**
 * Class Operator.
 */
class Operator
{
    /**
     * The wildcard operator.
     *
     * @var string
     */
    public static $wildcard = '*';

    /**
     * The equals operator.
     *
     * @var string
     */
    public static $equals = '=';

    /**
     * The does not equal operator.
     *
     * @var string
     */
    public static $doesNotEqual = '!';

    /**
     * The greater than or equal to operator.
     *
     * @var string
     */
    public static $greaterThanOrEqual = '>=';

    /**
     * The less than or equal to operator.
     *
     * @var string
     */
    public static $lessThanOrEqual = '<=';

    /**
     * The approximately equal to operator.
     *
     * @var string
     */
    public static $approximateEqual = '~=';

    /**
     * The and operator.
     *
     * @var string
     */
    public static $and = '&';

    /**
     * The or operator.
     *
     * @var string
     */
    public static $or = '|';

    /**
     * The custom starts with operator.
     *
     * @var string
     */
    public static $startsWith = 'starts_with';

    /**
     * The custom ends with operator.
     *
     * @var string
     */
    public static $endsWith = 'ends_with';

    /**
     * The custom contains operator.
     *
     * @var string
     */
    public static $contains = 'contains';
}
