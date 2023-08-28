<?php
declare(strict_types=1);

namespace Kentron\Facade\Env;

use Kentron\Support\File;
use Kentron\Support\Assert;
use Kentron\Facade\Env\Enum\EEnvironment;

use Dotenv\Dotenv;

use \Error;

class Env
{
    protected const DEFAULT_ENV_NAME = ".env";
    protected const DEFAULT_ENV_DIR = "/env";

    public static EEnvironment $environment;

    /** @var array<string,string|int|float|bool|null> $env */
    protected static $env = [];

    /**
     * Load the env file into the static prop
     *
     * @param string|null $directory
     * @param string|null $name
     *
     * @throws Error If no directory is provided and ROOT_DIR is not set
     * @throws Error If the env file is not readable
     * @throws Error If the env file is empty
     *
     * @return array<string,string|int|float|bool|null> The environment variables
     */
    public static function load (?string $directory = null, ?string $name = null): array
    {
        if (!empty(static::$env)) {
            return static::$env;
        }

        if (is_null($directory)) {
            if (!defined("ROOT_DIR")) {
                throw new Error("No env directory provided");
            }
            $directory = constant("ROOT_DIR") . static::DEFAULT_ENV_DIR;
        }

        $path = File::path($directory, $name ?? static::DEFAULT_ENV_NAME);

        $realPath = File::real($path);
        if (is_null($realPath)) {
            throw new Error("{$path} is not real");
        }

        if (!File::isReadable($realPath)) {
            throw new Error("{$realPath} is not readable");
        }

        $envContent = File::get($realPath);
        if (is_null($envContent)) {
            throw new Error("{$realPath} has no content");
        }

        foreach (Dotenv::parse($envContent) as $key => $env) {
            if ($env === '') {
                $env = null;
            }

            self::$env[$key] = $env;
        }

        return self::$env;
    }

    /**
     * Get the env array
     *
     * @return array The environment variables
     */
    public static function getEnv (): array
    {
        return self::$env;
    }

    /**
     * Get a value from the env array
     *
     * @param string $key
     *
     * @return string|int|float|bool|null
     */
    public static function getKey (string $key)
    {
        return self::$env[$key] ?? null;
    }

    /**
     * Returns true if the environment is set to development
     *
     * @return bool
     */
    public static function onDev(): bool
    {
        return Assert::same(self::$environment, EEnvironment::Dev)
            || Assert::same(self::$environment, EEnvironment::Docker)
        ;
    }
}
