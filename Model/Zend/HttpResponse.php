<?php

declare(strict_types=1);

namespace Volt\Payment\Model\Zend;

use Zend_Http_Exception;
use Zend_Http_Header_HeaderValue;

class HttpResponse extends \Zend_Http_Response
{
    /**
     * Extract the headers from a response string
     *
     * @param   string $response_str
     * @return  array
     */
    public static function extractHeaders($response_str)
    {
        $headers = array();

        // First, split body and headers. Headers are separated from the
        // message at exactly the sequence "\r\n\r\n"
        $parts = preg_split('|(?:\r\n){2}|m', $response_str, 2);
        if (! $parts[0]) {
            return $headers;
        }

        // Split headers part to lines; "\r\n" is the only valid line separator.
        $lines = explode("\r\n", $parts[0]);
        unset($parts);
        $last_header = null;

        foreach($lines as $index => $line) {
            if ($index === 0 && preg_match('#^HTTP/\d+(?:\.\d+)? [1-5]\d+#', $line)) {
                // Status line; ignore
                continue;
            }

            if ($line == "") {
                // Done processing headers
                break;
            }

            // Locate headers like 'Location: ...' and 'Location:...' (note the missing space)
            if (preg_match("|^([a-zA-Z0-9\'`#$%&*+.^_\|\~!-]+):\s*(.*)|s", $line, $m)) {
                unset($last_header);
                $h_name  = strtolower($m[1]);
                $h_value = $m[2];
                Zend_Http_Header_HeaderValue::assertValid($h_value);

                if (isset($headers[$h_name])) {
                    if (! is_array($headers[$h_name])) {
                        $headers[$h_name] = array($headers[$h_name]);
                    }

                    $headers[$h_name][] = ltrim($h_value);
                    $last_header = $h_name;
                    continue;
                }

                $headers[$h_name] = ltrim($h_value);
                $last_header = $h_name;
                continue;
            }

            // Identify header continuations
            if (preg_match("|^[ \t](.+)$|s", $line, $m) && $last_header !== null) {
                $h_value = trim($m[1]);
                if (is_array($headers[$last_header])) {
                    end($headers[$last_header]);
                    $last_header_key = key($headers[$last_header]);

                    $h_value = $headers[$last_header][$last_header_key] . $h_value;
                    Zend_Http_Header_HeaderValue::assertValid($h_value);

                    $headers[$last_header][$last_header_key] = $h_value;
                    continue;
                }

                $h_value = $headers[$last_header] . $h_value;
                Zend_Http_Header_HeaderValue::assertValid($h_value);

                $headers[$last_header] = $h_value;
                continue;
            }

            // Anything else is an error condition
            #require_once 'Zend/Http/Exception.php';
            throw new Zend_Http_Exception('Invalid header line detected');
        }

        return $headers;
    }
}
