<?php

namespace Kentron\Service;

use Kentron\Template\TError;

/**
 * A wrapper for the OPP CURL PHP system
 */
final class Curl
{
    use TError;

    /**
     * Curl instance
     * @var resource
     */
    private $curl;

    /**
     * Curl info from the response
     * @var array
     */
    private $curlInfo = [];

    /**
     * Response of the curl request
     * @var string
     */
    private $response = "";

    /**
     * Creates a new instance of curl on instantiation
     */
    public function __construct ()
    {
        $this->init();
    }

    /**
     * Getters
     */

    /**
     * Get info from the curl response
     * @return array
     */
    public function getInfo (): array
    {
        return $this->curlInfo;
    }

    /**
     * Get the curl response
     * @return string
     */
    public function getResponse (): string
    {
        return $this->response;
    }

    /**
     * Setters
     */

    /**
     * Set request method to GET
     * @return self
     */
    public function setGet(): self
    {
        return $this->setOpt(CURLOPT_POST, 0);
    }

    /**
     * Set the request headers
     * @param  array $headers
     * @return self
     */
    public function setHeaders (array $headers = []): self
    {
        return $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }

    public function setMethod (string $method): self
    {
        return $this->setOpt(CURLOPT_CUSTOMREQUEST, $method);
    }

    /**
     * Dynamic allocation of curl options
     * @param  int     $optName
     * @param  mixed   $value
     * @return self
     */
    public function setOpt (int $optName, $value): self
    {
        curl_setopt($this->curl, $optName, $value);
        return $this;
    }

    /**
     * Dynamic allocation of curl options with an array
     * @param  array $optArray Associative array of all options and their value
     * @return self
     */
    public function setOptArray (array $optArray): self
    {
        curl_setopt_array($this->curl, $optArray);
        return $this;
    }

    /**
     * Set the port
     * @param  int $portNumber
     * @return self
     */
    public function setPort (int $portNumber): self
    {
        return $this->setOpt(CURLOPT_PORT, $portNumber);
    }

    public function setPost ($postData): self
    {
        if (is_array($postData)) {
            $this->setPostArray($postData);
        }
        else if (is_string($postData)) {
            $this->setPostField($postData);
        }
        return $this;
    }

    /**
     * Set post data as array
     * @param  array $postData Post data associative array
     * @return self
     */
    public function setPostArray (array $postData): self
    {
        return $this->setOpt(CURLOPT_POST, true)
            ->setOpt(CURLOPT_POSTFIELDS, $postData);
    }

    /**
     * Set post data as string
     * @param  string $postData Raw post string
     * @return self
     */
    public function setPostField (string $postData): self
    {
        return $this->setOpt(CURLOPT_POST, true)
            ->setOpt(CURLOPT_POSTFIELDS, $postData);
    }

    /**
     * Set SSL keys
     * @param  string $sslPath Path to the SSL file
     * @param  string $sslPass Password
     * @return self
     */
    public function setSSL (string $sslPath = "/", string $sslPass = ""): self
    {
        return $this->setOpt(CURLOPT_SSLCERT, $sslPath)
            ->setOpt(CURLOPT_SSLCERTPASSWD, $sslPass);
    }

    /**
     * Set a timeout on the request
     * @param  int     $seconds
     * @return self
     */
    public function setTimeOut (int $seconds): self
    {
        return $this->setOpt(CURLOPT_TIMEOUT, $seconds);
    }

    /**
     * Set the target URL
     * @param  string $url
     * @return self
     */
    public function setUrl (string $url): self
    {
        return $this->setOpt(CURLOPT_URL, $url);
    }

    /**
     *
     * Helpers
     *
     */

    /**
     * Execute the curl request
     * @return bool True if request was successful
     */
    public function execute (): bool
    {
        $response       = curl_exec($this->curl);
        $errorNumber    = curl_errno($this->curl);
        $errorMessage   = curl_error($this->curl);
        $this->curlInfo = curl_getinfo($this->curl);

        $this->addError("Curl error ($errorNumber): $errorMessage");

        curl_close($this->curl);

        if ($response === false) {
            return false;
        }

        $this->response = $response;

        return true;
    }

    /**
     * Reset curl instance and clear errors
     */
    public function reset ()
    {
        $this->init();
    }

    /**
     *
     * Private functions
     *
     */

    /**
     * Initialise a new curl instance
     * @return resource
     */
    private function init ()
    {
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        $this->resetErrors();
    }
}
