<?php

namespace Kentron\Template;

/**
 * Error handling methods
 */
abstract class AAlert
{
    public const CODE_GROUP = 0;

    /**
     * The error array
     *
     * @var array
     */
    private $errors = [];

    /**
     * The notice array
     *
     * @var array
     */
    private $notices = [];

    /**
     * The warning array
     *
     * @var array
     */
    private $warnings = [];

    /**
     * The error codes
     *
     * @var integer
     */
    private $errorCodes = 0;

    /**
     * Return the full array of errors
     *
     * @return array
     */
    final public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Return the full array of notices
     *
     * @return array
     */
    final public function getNotices(): array
    {
        return $this->notices;
    }

    /**
     * Return the full array of warnings
     *
     * @return array
     */
    final public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * Add one or an array of errors
     *
     * @param string|array $errors
     */
    final public function addError($errors): void
    {
        if (is_string($errors)) {
            $this->errors[] = $errors;
        }
        else if (is_array($errors)) {
            $this->errors = array_merge($this->errors, $errors);
        }
    }

    /**
     * Add one or an array of notices
     *
     * @param string|array $notices
     */
    final public function addNotice($notices): void
    {
        if (is_string($notices)) {
            $this->notices[] = $notices;
        }
        else if (is_array($notices)) {
            $this->notices = array_merge($this->notices, $notices);
        }
    }

    /**
     * Add one or an array of warnings
     *
     * @param string|array $warnings
     */
    final public function addWarning($warnings): void
    {
        if (is_string($warnings)) {
            $this->warnings[] = $warnings;
        }
        else if (is_array($warnings)) {
            $this->warnings = array_merge($this->warnings, $warnings);
        }
    }

    /**
     * Checks if any errors have been added to the array
     *
     * @return boolean
     */
    final public function hasErrors(): bool
    {
        return (count($this->errors) > 0);
    }

    /**
     * Checks if any notices have been added to the array
     *
     * @return boolean
     */
    final public function hasNotices(): bool
    {
        return (count($this->notices) > 0);
    }

    /**
     * Checks if any warnings have been added to the array
     *
     * @return boolean
     */
    final public function hasWarnings(): bool
    {
        return (count($this->warnings) > 0);
    }

    /**
     * Empty the array of errors
     *
     * @return void
     */
    final public function resetErrors(): void
    {
        $this->errors = [];
    }

    /**
     * Empty the array of notices
     *
     * @return void
     */
    final public function resetNotices(): void
    {
        $this->notices = [];
    }

    /**
     * Empty the array of warnings
     *
     * @return void
     */
    final public function resetWarnings(): void
    {
        $this->warnings = [];
    }

    final public function addErrorCode(int $errorCode): void
    {
        $this->errorCodes |= $errorCode;
    }

    final public function hasErrorCode(int $errorCode): bool
    {
        return $this->errorCodes & $errorCode;
    }

    final public function hasErrorCodes(): bool
    {
        return $this->errorCodes !== 0;
    }

    final public function getErrorCodes(): int
    {
        return $this->errorCodes;
    }

    /**
     * Merge the errors, notices and warnings with another entity
     *
     * @param self $alert
     *
     * @return void
     */
    final public function mergeAlerts(self $alert): void
    {
        $this->addError($alert->getErrors());
        $this->addNotice($alert->getNotices());
        $this->addWarning($alert->getWarnings());
    }

    final public function normaliseAlerts(): ?array
    {
        $normalised = [];

        if ($this->hasErrors()) {
            $normalised["errors"] = $this->getErrors();
        }
        if ($this->hasNotices()) {
            $normalised["notices"] = $this->getNotices();
        }
        if ($this->hasWarnings()) {
            $normalised["warnings"] = $this->getWarnings();
        }

        return $normalised ?: null;
    }
}
