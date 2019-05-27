<?php

    namespace Kentron\Facade;

    use Swift_SmtpTransport;
    use Swift_Message;
    use Swift_Mailer;

    final class Mail
    {
        /**
         * If Swiftmailer produces an error, it goes here
         * @var string
         */
        public $error = "";

        /**
         * Count of all emails successfully delivered
         * @var int
         */
        public $mailSentCount;

        /**
         * Username for the mailer transport
         * @var string
         */
        private $_username = "";

        /**
         * Password for the mailer transport
         * @var string
         */
        private $_password = "";

        /**
         * SMTP domain
         * @var string
         */
        private $_smtp = "smtp.office365.com";

        /**
         * Port number
         * @var int
         */
        private $_port = 587;

        /**
         * Mail protocol
         * @var string
         */
        private $_method = "tls";

        /**
         * Origin email address
         * @var string
         */
        private $_fromEmail = "";

        /**
         * Origin display name
         * @var string
         */
        private $_fromName = "";

        /**
         * Content type of the email body
         * @var string
         */
        private $_contentType;

        /**
         * The mailer transport
         * @var Swift_SmtpTransport
         */
        private $_transport;

        /**
         * @param   string|null $username
         * @param   string|null $password
         * @param   string|null $smtp
         * @param   int|null    $port
         * @param   string|null $method
         */
        public function __construct (?string $username = null, ?string $password = null, ?string $smtp = null, ?int $port = null, ?string $method = null)
        {
            if (is_string($username)) {
                $this->_username = $username;
            }

            if (is_string($password)) {
                $this->_password = $password;
            }

            if (is_string($smtp)) {
                $this->_smtp = $smtp;
            }

            if (is_int($port)) {
                $this->_port = $port;
            }

            if (is_string($method)) {
                $this->_method = $method;
            }
        }

        private function buildTransport (): void
        {
            $this->_transport = new Swift_SmtpTransport($this->_smtp, $this->_port, $this->_method);

            $this->_transport->setUsername($this->_username);
            $this->_transport->setPassword($this->_password);
        }

        public function send (string $to, string $subject, string $body = "")
        {
            $message = new Swift_Message();

            $message->setFrom([$this->_fromEmail => $this->_fromName]);
            $message->setTo($to);
            $message->setSubject($subject);

            $message->setBody($body);
            $message->setContentType($this->_contentType);

            try {
                $mailer = new Swift_Mailer($this->_transport);
                $this->mailSentCount = $mailer->send($message);
            }
            catch (\Exception $ex) {
                $this->error = $ex->getMessage();
                return false;
            }

            return true;
        }

        /**
         *
         * Methods
         *
         */
        public function setUsername (string $username): void
        {
            $this->_username = $username;
        }

        public function setPassword (string $password): void
        {
            $this->_password = $password;
        }

        public function setSmtp (string $smtp): void
        {
            $this->_smtp = $smtp;
        }

        public function setPort (int $port): void
        {
            $this->_port = $port;
        }

        public function setMethod (string $method): void
        {
            $this->_method = $method;
        }

        public function setFromEmail (string $fromEmail): void
        {
            $this->_fromEmail = $fromEmail;
        }

        public function setFromName (string $fromName): void
        {
            $this->_fromName = $fromName;
        }

        public function setContentType (string $contentType): void
        {
            $this->_contentType = $contentType;
        }

    }
