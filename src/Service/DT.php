<?php

namespace Kentron\Service;

use \DateTime;

final class DT extends DateTime
{
    public static function now (): self
    {
        return self::then();
    }

    public static function then (?string $timeStamp = null): self
    {
        return new self($timeStamp);
    }

    public function increment (int $seconds): self
    {
        return $this->add(new \DateInterval("PT{$seconds}S"));
    }

    public function format ($format = ""): string
    {
        return parent::format($format ?: "c");
    }

    public function strSplit (): array
    {
        return [
            $this->format("Y"),
            $this->format("m"),
            $this->format("d"),
            $this->format("H"),
            $this->format("i"),
            $this->format("s"),
        ];
    }

    public function intSplit (): array
    {
        return [
            $this->format("Y"),
            $this->format("n"),
            $this->format("j"),
            $this->format("G"),
            (int) $this->format("i"),
            (int) $this->format("s"),
        ];
    }

    public function compare (?self $date = null): int
    {
        $date = $date ?? self::now();
        return $this->format() <=> $date->format();
    }
}
