<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * A webhook target has to be somewhere on the public internet.
 *
 * Without this, an action_url can name any host the app container can reach
 * - the database, another container on the upthing network, or the cloud
 * metadata service on 169.254.169.254 - and the webhook machinery will
 * happily make the request on the user's behalf.
 */
class PublicHttpsUrl implements Rule
{
    private $message = 'The :attribute must be a https URL on a public host.';

    /**
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!is_string($value)) {
            return false;
        }

        $parts = parse_url($value);
        if ($parts === false || !isset($parts['scheme']) || !isset($parts['host'])) {
            return false;
        }

        if (strtolower($parts['scheme']) !== 'https') {
            $this->message = 'The :attribute must use https.';
            return false;
        }

        // parse_url keeps the brackets on an IPv6 literal.
        $host = trim($parts['host'], '[]');

        $addresses = $this->resolve($host);
        if (empty($addresses)) {
            $this->message = 'The :attribute host could not be resolved.';
            return false;
        }

        foreach ($addresses as $address) {
            if (!$this->isPublic($address)) {
                $this->message = 'The :attribute must not point at a private or reserved address.';
                return false;
            }
        }

        return true;
    }

    /**
     * Every address the host resolves to - one bad record is enough to make
     * the target unsafe.
     *
     * @param  string  $host
     * @return array
     */
    private function resolve($host)
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return [$host];
        }

        $addresses = [];

        $v4 = gethostbynamel($host);
        if (is_array($v4)) {
            $addresses = $v4;
        }

        $v6 = @dns_get_record($host, DNS_AAAA);
        if (is_array($v6)) {
            foreach ($v6 as $record) {
                if (isset($record['ipv6'])) {
                    $addresses[] = $record['ipv6'];
                }
            }
        }

        return $addresses;
    }

    /**
     * @param  string  $address
     * @return bool
     */
    private function isPublic($address)
    {
        return (bool)filter_var(
            $address,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    /**
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
