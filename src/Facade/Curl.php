<?php

namespace Kentron\Facade;

use Kentron\Template\AAlert;

/**
 * A wrapper for the OPP CURL PHP system
 */
final class Curl extends AAlert
{
    /**
     * Curl instance
     *
     * @var resource
     */
    private $curl;

    /**
     * Curl info from the response
     *
     * @var array
     */
    private $curlInfo = [];

    /**
     * Response of the curl request
     *
     * @var string
     */
    private $response = "";

    /**
     * The response status code
     *
     * @var int
     */
    private $statusCode;

    /**
     * Creates a new instance of curl on instantiation
     */
    public function __construct ()
    {
        $this->init();
    }

    public function __destruct ()
    {
        curl_close($this->curl);
    }

    /**
     * Getters
     */

    /**
     * Get info from the curl response
     *
     * @return array
     */
    public function getInfo (): array
    {
        return $this->curlInfo;
    }

    /**
     * Get the curl response
     *
     * @return string
     */
    public function getResponse (): string
    {
        return $this->response;
    }

    public function getStatusCode (): int
    {
        return $this->statusCode;
    }

    /**
     * Setters
     */

    /**
     * Set request method to GET
     *
     * @return void
     */
    public function setGet(): void
    {
        $this->setOpt(CURLOPT_POST, 0);
    }

    /**
     * Set the request headers
     *
     * @param array $headers
     *
     * @return void
     */
    public function setHeaders (array $headers = []): void
    {
        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }

    public function setMethod (string $method): void
    {
        $this->setOpt(CURLOPT_CUSTOMREQUEST, $method);
    }

    /**
     * Dynamic allocation of curl options
     *
     * @param int   $optName
     * @param mixed $value
     *
     * @return void
     */
    public function setOpt (int $optName, $value): void
    {
        curl_setopt($this->curl, $optName, $value);
    }

    /**
     * Dynamic allocation of curl options with an array
     *
     * @param array $optArray Associative array of all options and their value
     *
     * @return void
     */
    public function setOptArray (array $optArray): void
    {
        curl_setopt_array($this->curl, $optArray);
    }

    /**
     * Set the port
     *
     * @param int $portNumber
     *
     * @return void
     */
    public function setPort (int $portNumber): void
    {
        $this->setOpt(CURLOPT_PORT, $portNumber);
    }


    /**
     * Sets the post data
     *
     * @param array|string $postData
     *
     * @return void
     */
    public function setPost ($postData): void
    {
        if (is_array($postData))
        {
            $this->setPostArray($postData);
        }
        else if (is_string($postData))
        {
            $this->setPostField($postData);
        }
    }

    /**
     * Set post data as array
     *
     * @param array $postData Post data associative array
     *
     * @return void
     */
    public function setPostArray (array $postData): void
    {
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $postData);
    }

    /**
     * Set post data as string
     *
     * @param string $postData Raw post string
     *
     * @return void
     */
    public function setPostField (string $postData): void
    {
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $postData);
    }

    /**
     * Set SSL keys
     *
     * @param string $sslPath Path to the SSL file
     * @param string $sslPass Password
     *
     * @return void
     */
    public function setSSL (string $sslPath = "/", string $sslPass = ""): void
    {
        $this->setOpt(CURLOPT_SSLCERT, $sslPath);
        $this->setOpt(CURLOPT_SSLCERTPASSWD, $sslPass);
    }

    /**
     * Set a timeout on the request
     *
     * @param int $seconds
     *
     * @return void
     */
    public function setTimeOut (int $seconds): void
    {
        $this->setOpt(CURLOPT_TIMEOUT, $seconds);
    }

    /**
     * Set the target URL
     *
     * @param string $url
     *
     * @return void
     */
    public function setUrl (string $url): void
    {
        $this->setOpt(CURLOPT_URL, $url);
    }

    /**
     * Sets the request method
     *
     * @param string $customRequest
     *
     * @return void
     */
    public function setRequestMethod (string $customRequest): void
    {
        $this->setOpt(CURLOPT_CUSTOMREQUEST, $customRequest);
    }

    /**
     *
     * Helpers
     *
     */

    /**
     * Execute the curl request
     *
     * @return bool True if request was successful
     */
    public function execute (): bool
    {
        $response = curl_exec($this->curl);
        $this->curlInfo = curl_getinfo($this->curl);
        $this->statusCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        if ($response === false)
        {
            $errorNumber = curl_errno($this->curl);
            $errorMessage = curl_error($this->curl);

            $this->addError("Curl error ($errorNumber): $errorMessage");
            return false;
        }

        $this->response = $response;

        return true;
    }

    /**
     * Reset curl instance and clear errors
     *
     * @return void
     */
    public function reset (): void
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
     *
     * @return void
     */
    private function init (): void
    {
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        $this->resetErrors();
    }
}
