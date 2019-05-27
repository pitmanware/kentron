<?php

    namespace Kentron\Proxy;

    /**
     * A wrapper class for proc_open
     * @var [type]
     */
    final class Command
    {
        /**
         * Whether to escape any argument passed through addArg()
         * @var boolean
         */
        public $escapeArgs = true;

        /**
         * Whether to escape the command passed to setCommand()
         * This is only useful if $escapeArgs is false. Default is false
         * @var bool
         */
        public $escapeCommand = false;

        /**
         * The locale to temporarily set before calling escapeshellargs()
         * @var string
         */
        public $locale = "en_US.UTF-8";

        /**
         * The initial working dir for proc_open()
         * @var string|null
         */
        public $procCwd;

        /**
         * An array with environment variables to pass to proc_open()
         * @var array|null
         */
        public $procEnv;

        /**
         * An array of options for proc_open()
         * @var array|null
         */
        public $procOpt;

        /**
         * The list of command arguments
         * @var array
         */
        private $args = [];

        /**
         * The command to execute
         * @var string|null
         */
        private $command = null;

        /**
         * The error message
         * @var string
         */
        private $error = "";

        /**
         * The full command string to execute
         * @var string
         */
        private $execCommand;

        /**
         * The exit code
         * @var int
         */
        private $exitCode;

        /**
         * Standard input
         * @var null|string|resource
         */
        private $stdIn;

        /**
         * Standard output
         * @var string
         */
        private $stdOut = "";

        /**
         * Standard error
         * @var string
         */
        private $stdErr = "";

        /**
         * Whether the command was successfully executed
         * @var bool
         */
        private $success = false;

        /**
         *
         * Getters
         *
         */

        /**
         * @return array the command arguments
         */
        public function getArgs (): array
        {
            return $this->args;
        }

        /**
         * @return string|null the command that was set through setCommand() or passed to the constructor.
         */
        public function getCommand (): ?string
        {
            return $this->command;
        }

        /**
         * @return string the error message, either stderr or internal message. Empty if none.
         */
        public function getError (): string
        {
            return trim($this->error);
        }

        /**
         * @return string|null the full command string to execute. If no command was set with setCommand()
         * or passed to the constructor it will return null.
         */
        public function getExecCommand (): ?string
        {
            $this->execCommand = $this->getCommand();

            if (is_string($this->execCommand)) {
                $argString = implode(" ", $this->getArgs());
                $this->execCommand += " $argString";
            }

            return $this->execCommand;
        }

        /**
         * @return int|null the exit code or null if command was not executed yet
         */
        public function getExitCode (): ?int
        {
            return $this->exitCode;
        }

        /**
         * @return string the stderr output. Empty if none.
         */
        public function getStdErr (): string
        {
            return trim($this->stdErr);
        }

        /**
         * @return string the command output (stdout). Empty if none.
         */
        public function getStdOut (): string
        {
            return trim($this->stdOut);
        }

        /**
         * @return bool whether the command was successfully executed
         */
        public function getSuccess (): bool
        {
            return $this->success;
        }

        /**
         *
         * Setters
         *
         */

        /**
         * @param string $args the command arguments as string. Note that these will not get escaped!
         * @return self
         */
        public function setArgs (string $args): self
        {
            $this->args = [$args];
            return $this;
        }

        /**
         * @param string $command the command to execute.
         * If $escapeCommand was set to true, the command gets escaped through escapeshellcmd().
         * @return self
         */
        public function setCommand (string $command): self
        {
            if ($this->escapeCommand) {
                $command = escapeshellcmd($command);
            }
            $this->command = $command;

            return $this;
        }

        /**
         * @param string|resource $stdIn if set, the string will be piped to the command via standard input.
         * This enables the same functionality as piping on the command line.
         * It can also be a resource like a file handle or a stream in which case
         * its content will be piped into the command like an input redirection.
         * @return self
         */
        public function setStdIn ($stdIn): self
        {
            $this->stdIn = $stdIn;
            return $this;
        }

        /**
         *
         * Helpers
         *
         */

        /**
         * @param string $option the argument to add e.g. '--feature' or '--name='. If the option does not end with
         * and '=', any $params will be separated by a space. Options are not escaped unless $params is null
         * and $escapeOverride is true.
         * @param array|null $params the optional argument value which will get escaped if $escapeArgs is true.
         * An array can be passed to add one or more values for a option, e.g. addArg("--exclude", ["val1", "val2"])
         * which will create the option '--exclude 'val1" "val2"'.
         * @param bool $escapeOverride if set, this enforces escaping
         * @return self
         */
        public function addArg (string $option, ?array $params = null, ?bool $escapeOverride = false): self
        {
            $escape = $escapeOverride || $this->escapeArgs;

            if ($escape) {
                // Save current locale
                $locale = setlocale(LC_CTYPE, 0);
                setlocale(LC_CTYPE, $this->locale);
            }

            if (is_null($params)) {
                // Only escape single arguments if explicitly requested
                $this->args[] = $escapeOverride ? escapeshellarg($option) : $option;
            }
            else {
                $separator = substr($option, -1) === "=" ? "" : " ";
                $paramList = [];

                foreach ($params as $param) {
                    $paramList[] = $escape ? escapeshellarg($param) : $param;
                }

                $this->args[] = "$option$separator" . implode(" ", $paramList);
            }

            if ($escape) {
                // Reset locale to its original state
                setlocale(LC_CTYPE, $locale);
            }

            return $this;
        }

        /**
         * Execute the command
         *
         * @return bool whether execution was successful. If false, error details can be obtained through
         * getError(), getStdErr() and getExitCode().
         */
        public function execute (): bool
        {
            $command = $this->getExecCommand();

            if (is_null($command)) {
                return false;
            }

            $descriptors = [
                1 => ["pipe", "w"],
                2 => ["pipe", "w"],
            ];

            if (!is_null($this->stdIn)) {
                $descriptors[0] = ["pipe", "r"];
            }

            $process = proc_open($command, $descriptors, $pipes, $this->procCwd, $this->procEnv, $this->procOptions);

            if (is_resource($process)) {

                if (is_null($this->stdIn)) {

                    if (is_resource($this->stdIn) && get_resource_type($this->stdIn) === "stream") {
                        stream_copy_to_stream($this->stdIn, $pipes[0]);
                    }
                    else {
                        fwrite($pipes[0], $this->stdIn);
                    }
                    fclose($pipes[0]);
                }

                $this->stdOut = stream_get_contents($pipes[1]);
                $this->stdErr = stream_get_contents($pipes[2]);

                fclose($pipes[1]);
                fclose($pipes[2]);

                $this->exitCode = proc_close($process);

                if ($this->exitCode !== 0) {
                    $this->error = $this->stdErr ? $this->stdErr : "Failed without error message: $command";
                    return false;
                }
            }
            else {
                $this->error = "Could not run command: $command";
                return false;
            }

            return $this->success = true;
        }
    }
