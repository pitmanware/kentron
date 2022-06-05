<?php
declare(strict_types=1);

namespace Kentron\Support\System;

final class Files
{
    public function getOne(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    public function getAll(): array
    {
        return $_FILES ?? [];
    }

    /**
     * Checks if the file(s) is/are valid
     *
     * @return string|null Returns null if nothing went wrong, otherwise, returns an error
     */
    public static function validateFiles(): ?string
    {
        if (
            !isset($_FILES["upfile"]["error"]) ||
            !is_int($_FILES["upfile"]["error"])
        ) {
            return "Invalid parameters.";
        }

        switch ($_FILES["upfile"]["error"]) {
            case UPLOAD_ERR_OK:
                break;

            case UPLOAD_ERR_NO_FILE:
                return "No file sent.";

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return "Exceeded filesize limit.";

            default:
                return "Unknown error.";
        }

        if ($_FILES["upfile"]["size"] > 1000000) {
            return "Exceeded filesize limit.";
        }

        return null;
    }
}
