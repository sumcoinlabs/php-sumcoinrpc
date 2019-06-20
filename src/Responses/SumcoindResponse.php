<?php

declare(strict_types=1);

namespace Denpa\Sumcoin\Responses;

use Denpa\Sumcoin\Traits\Collection;
use Denpa\Sumcoin\Traits\ImmutableArray;
use Denpa\Sumcoin\Traits\SerializableContainer;

class BitcoindResponse extends Response implements
    \ArrayAccess,
    \Countable,
    \Serializable,
    \JsonSerializable
{
    use Collection, ImmutableArray, SerializableContainer;

    /**
     * Gets array representation of response object.
     *
     * @return array
     */
    public function toArray() : array
    {
        return (array) $this->result();
    }

    /**
     * Gets root container of response object.
     *
     * @return array
     */
    public function toContainer() : array
    {
        return $this->container;
    }
}
