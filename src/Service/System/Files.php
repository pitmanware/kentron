<?php

namespace Kentron\Service\System;

use Kentron\Template\TError;

final class Files
{
    use TError;

    public function getOne (string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    public function getAll (): array
    {
        return $_FILES ?? [];
    }

    public function isValid (): bool
    {
        if (
            !isset($_FILES["upfile"]["error"]) ||
            !is_int($_FILES["upfile"]["error"])
        ) {
            $this->addError("Invalid parameters.");
            return false;
        }

        switch ($_FILES["upfile"]["error"]) {
            case UPLOAD_ERR_OK:
                break;

            case UPLOAD_ERR_NO_FILE:
                $this->addError("No file sent.");
                return false;

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->addError("Exceeded filesize limit.");
                return false;

            default:
                $this->addError("Unknown error.");
                return false;
        }

        if ($_FILES["upfile"]["size"] > 1000000) {
            $this->addError("Exceeded filesize limit.");
            return false;
        }
    }
}
