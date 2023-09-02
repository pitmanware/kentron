<?php
declare(strict_types=1);

namespace Kentron\Template\Alert;

use Kentron\Template\AClass;

/**
 * Error handling methods
 */
abstract class AAlert extends AClass
{
    use TError;
    use TErrorCode;
    use TNotice;
    use TWarning;

    /**
     * Merge the errors, notices and warnings with another entity
     *
     * @param self $alert
     *
     * @return void
     */
    final public function mergeAlerts(self $alert): void
    {
        $this->mergeErrors($alert);
        $this->mergeErrorCodes($alert);
        $this->mergeNotices($alert);
        $this->mergeWarnings($alert);
    }

    /**
     * Return any errors, notices and warnings as keyed arrays
     *
     * @return array<string,string[]>
     */
    final public function normaliseAlerts(): array
    {
        return array_merge(
            $this->normaliseErrors(),
            $this->normaliseNotices(),
            $this->normaliseWarnings()
        );
    }
}
