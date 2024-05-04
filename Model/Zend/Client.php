<?php

declare(strict_types=1);

namespace Volt\Payment\Model\Zend;

use Magento\Framework\HTTP\ZendClient;
use Zend_Http_Client_Adapter_Stream;
use Zend_Http_Client_Exception;
use Zend_Http_Response_Stream;
use Zend_Uri_Http;

class Client extends ZendClient
{
    public function request($method = null)
    {
        if (! $this->uri instanceof Zend_Uri_Http) {
            /** @see Zend_Http_Client_Exception */
            #require_once 'Zend/Http/Client/Exception.php';
            throw new Zend_Http_Client_Exception('No valid URI has been passed to the client');
        }

        if ($method) {
            $this->setMethod($method);
        }
        $this->redirectCounter = 0;
        $response = null;

        // Make sure the adapter is loaded
        if ($this->adapter == null) {
            $this->setAdapter($this->config['adapter']);
        }

        // Send the first request. If redirected, continue.
        do {
            // Clone the URI and add the additional GET parameters to it
            $uri = clone $this->uri;
            if (! empty($this->paramsGet)) {
                $query = $uri->getQuery();
                if (! empty($query)) {
                    $query .= '&';
                }
                $query .= http_build_query($this->paramsGet, null, '&');
                if ($this->config['rfc3986_strict']) {
                    $query = str_replace('+', '%20', $query);
                }

                // @see ZF-11671 to unmask for some services to foo=val1&foo=val2
                if ($this->getUnmaskStatus()) {
                    if ($this->_queryBracketsEscaped) {
                        $query = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $query);
                    } else {
                        $query = preg_replace('/\\[(?:[0-9]|[1-9][0-9]+)\\]=/', '=', $query);
                    }
                }

                $uri->setQuery($query);
            }

            $body = $this->_prepareBody();
            $headers = $this->_prepareHeaders();

            // check that adapter supports streaming before using it
            if(is_resource($body) && !($this->adapter instanceof Zend_Http_Client_Adapter_Stream)) {
                /** @see Zend_Http_Client_Exception */
                #require_once 'Zend/Http/Client/Exception.php';
                throw new Zend_Http_Client_Exception('Adapter does not support streaming');
            }

            // Open the connection, send the request and read the response
            $this->adapter->connect($uri->getHost(), $uri->getPort(),
                ($uri->getScheme() == 'https' ? true : false));

            if($this->config['output_stream']) {
                if($this->adapter instanceof Zend_Http_Client_Adapter_Stream) {
                    $stream = $this->_openTempStream();
                    $this->adapter->setOutputStream($stream);
                } else {
                    /** @see Zend_Http_Client_Exception */
                    #require_once 'Zend/Http/Client/Exception.php';
                    throw new Zend_Http_Client_Exception('Adapter does not support streaming');
                }
            }

            $this->last_request = $this->adapter->write($this->method,
                $uri, $this->config['httpversion'], $headers, $body);

            $response = $this->adapter->read();
            if (! $response) {
                /** @see Zend_Http_Client_Exception */
                #require_once 'Zend/Http/Client/Exception.php';
                throw new Zend_Http_Client_Exception('Unable to read response, or response is empty');
            }

            if($this->config['output_stream']) {
                $streamMetaData = stream_get_meta_data($stream);
                if ($streamMetaData['seekable']) {
                    rewind($stream);
                }
                // cleanup the adapter
                $this->adapter->setOutputStream(null);
                $response = Zend_Http_Response_Stream::fromStream($response, $stream);
                $response->setStreamName($this->_stream_name);
                if(!is_string($this->config['output_stream'])) {
                    // we used temp name, will need to clean up
                    $response->setCleanup(true);
                }
            } else {
                $response = HttpResponse::fromString($response);
            }

            if ($this->config['storeresponse']) {
                $this->last_response = $response;
            }

            // Load cookies into cookie jar
            if (isset($this->cookiejar)) {
                $this->cookiejar->addCookiesFromResponse($response, $uri, $this->config['encodecookies']);
            }

            // If we got redirected, look for the Location header
            if ($response->isRedirect() && ($location = $response->getHeader('location'))) {

                // Avoid problems with buggy servers that add whitespace at the
                // end of some headers (See ZF-11283)
                $location = trim($location);

                // Check whether we send the exact same request again, or drop the parameters
                // and send a GET request
                if ($response->getStatus() == 303 ||
                    ((! $this->config['strictredirects']) && ($response->getStatus() == 302 ||
                            $response->getStatus() == 301))) {

                    $this->resetParameters();
                    $this->setMethod(self::GET);
                }

                // If we got a well formed absolute URI
                if (($scheme = substr($location, 0, 6)) && ($scheme == 'http:/' || $scheme == 'https:')) {
                    $this->setHeaders('host', null);
                    $this->setUri($location);

                } else {

                    // Split into path and query and set the query
                    if (strpos($location, '?') !== false) {
                        list($location, $query) = explode('?', $location, 2);
                    } else {
                        $query = '';
                    }
                    $this->uri->setQuery($query);

                    // Else, if we got just an absolute path, set it
                    if(strpos($location, '/') === 0) {
                        $this->uri->setPath($location);

                        // Else, assume we have a relative path
                    } else {
                        // Get the current path directory, removing any trailing slashes
                        $path = $this->uri->getPath();
                        $path = rtrim(substr($path, 0, strrpos($path, '/')), "/");
                        $this->uri->setPath($path . '/' . $location);
                    }
                }
                ++$this->redirectCounter;

            } else {
                // If we didn't get any location, stop redirecting
                break;
            }

        } while ($this->redirectCounter < $this->config['maxredirects']);

        return $response;
    }
}
