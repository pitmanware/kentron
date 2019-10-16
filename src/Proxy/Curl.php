<?php

    namespace Kentron\Proxy;

    /**
     * A wrapper for the OPP CURL PHP system
     */
    final class Curl
    {
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
         * The curl error if applicable
         * @var string|null
         */
        private $error = null;

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
            $this->curl = $this->init();
        }

        /**
         * Getters
         */

        /**
        * Get curl error
        * @return string|null
        */
        public function getError (): ?string
        {
            return $this->error;
        }

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
            curl_setopt($this->curl, CURLOPT_POST, 0);
            return $this;
        }

        /**
         * Set the request headers
         * @param  array $headers
         * @return self
         */
        public function setHeaders (array $headers = []): self
        {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
            return $this;
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
            curl_setopt($this->curl, CURLOPT_PORT, $portNumber);
            return $this;
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
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postData);
            return $this;
        }

        /**
         * Set post data as string
         * @param  string $postData Raw post string
         * @return self
         */
        public function setPostField (string $postData): self
        {
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postData);
            return $this;
        }

        /**
         * Set SSL keys
         * @param  string $sslPath Path to the SSL file
         * @param  string $sslPass Password
         * @return self
         */
        public function setSSL (string $sslPath = "/", string $sslPass = ""): self
        {
            curl_setopt($this->curl, CURLOPT_SSLCERT, $sslPath);
            curl_setopt($this->curl, CURLOPT_SSLCERTPASSWD, $sslPass);
            return $this;
        }

        /**
         * Set a timeout on the request
         * @param  int     $seconds
         * @return self
         */
        public function setTimeOut (int $seconds): self
        {
            curl_setopt($this->curl, CURLOPT_TIMEOUT, $seconds);
            return $this;
        }

        /**
         * Set the target URL
         * @param  string $url
         * @return self
         */
        public function setUrl (string $url): self
        {
            curl_setopt($this->curl, CURLOPT_URL, $url);
            return $this;
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
            $this->error    = "Curl error ($errorNumber): $errorMessage";
            $this->curlInfo = curl_getinfo($this->curl);

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
            $this->curl     = $this->init();
            $this->errors   = [];
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
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            return $curl;
        }
    }
