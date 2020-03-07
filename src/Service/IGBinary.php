<?php

namespace Kentron\Service;

final class IGBinary
{
    /**
     * Serialise data to a binary string
     *
     * @param mixed $decoded The data to be encoded
     *
     * @return string|null The encoded binary string
     */
    public static function serialise ($decoded): ?string
    {
        if (is_null($decoded) || is_resource($decoded))
        {
            return null;
        }

        return @igbinary_serialize($decoded);
    }

    /**
     * Unserialise the encoded binary string back to usable data
     *
     * @param string $encoded The binary encoded string
     *
     * @return mixed The decoded data
     */
    public static function unserialise (string $encoded)
    {
        return @igbinary_unserialize($encoded);
    }
}
