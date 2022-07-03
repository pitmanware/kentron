<?php
declare(strict_types=1);

namespace Kentron\Template\Store;

class LocalStore implements IStore
{
    /** The auth ID of the application */
    public static int|null $authId = null;

    /** The audit ID from the current request */
    public static int|null $auditId = null;

    /** The external reference ID */
    public static int|null $externalReferenceId = null;

    /** The session ID that all queries will need for reference */
    public static int|null $sessionId = null;

    /** The audit ID associated to the session */
    public static int|null $sessionAuditId = null;

    /** The unique token associated to the session */
    public static string|null $sessionToken = null;

    /**
     * Helpers
     */

    public static function reset(bool $hard = false): void
    {
        self::$authId = null;
        self::$auditId = null;
        self::$externalReferenceId = null;
        self::$sessionId = null;
        self::$sessionAuditId = null;
        self::$sessionToken = null;
    }
}
