<?php
declare(strict_types=1);

namespace Kentron\Facade;

use Kentron\Support\Type\Type;
use Kentron\Support\File;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

use \InvalidArgumentException;

/**
 * FIX: This expects a package that has been removed, it needs to be abstracted
 */
class View
{
    public string $title = "";
    public mixed $data = [];

    private string $directory = "";
    private string $index = "index.twig";
    private string|null $frame = null;
    private array $scripts = [];
    private array $styles = [];

    public function __construct(?string $directory = null, ?string $index = null, ?string $frame = null)
    {
        $this->includeMasterScripts();
        $this->includeMasterStyles();

        if (is_string($directory)) {
            $this->setDirectory($directory);
        }

        if (is_string($index)) {
            $this->setIndex($index);
        }

        if (is_string($frame)) {
            $this->setFrame($frame);
        }
    }

    /**
     * Setters
     */

    /**
     * Set the base directory for the index file
     *
     * @param string $directory The absolute directory path
     *
     * @throws InvalidArgumentException if the directory is invalid
     */
    public function setDirectory(string $directory): void
    {
        $dir = File::real($directory);
        if (is_null($dir) || !File::isValidDir($dir)) {
            throw new \InvalidArgumentException("'{$directory}' is not a valid directory");
        }

        $this->directory = $dir;
    }

    public function setIndex(string $index): void
    {
        $file = File::real($this->addExtension($index));
        if (is_null($file) || !File::isValidFile($file)) {
            throw new \InvalidArgumentException("'{$index}' is not a valid file");
        }

        $this->index = $file;
    }

    public function setFrame(string $frame): void
    {
        $file = File::real($this->addExtension($frame));
        if (is_null($file) || !File::isValidFile($file)) {
            throw new \InvalidArgumentException("'{$frame}' is not a valid file");
        }

        $this->frame = $file;
    }

    public function setAlerts(array $alerts): void
    {
        $this->addData("alerts", $alerts);
    }

    /**
     * Adders
     */

    public function addData($data, $value = null): void
    {
        if (is_null($value)) {
            $this->data += Type::castToArray($data);
        }
        else {
            $this->data[Type::castToString($data)] = $value;
        }
    }

    public function addScripts($scripts): void
    {
        $scripts = Type::castToArray($scripts);

        foreach ($scripts as $script) if (is_string($script)) {
            $this->scripts[] = $script;
        }
    }

    public function addStyles(array $styles): void
    {
        $styles = Type::castToArray($styles);

        foreach ($styles as $style) if (is_string($style)) {
            $this->scripts[] = $style;
        }
    }

    /**
     * Getters
     */

    public function getProperties(): array
    {
        return [
            "meta" => [
                "title" => $this->title,
            ],
            "scripts" => $this->scripts,
            "styles" => $this->styles,
            "frame" => $this->frame,
            "data" => $this->data
        ];
    }

    /**
     * Helpers
     */

    public function includeMasterScripts(bool $include = true): void
    {
        $this->data["include_master_scripts"] = $include;
    }

    public function includeMasterStyles(bool $include = true): void
    {
        $this->data["include_master_styles"] = $include;
    }

    public function render(array $data = []): void
    {
        echo $this->capture($data);
    }

    public function capture(array $data = []): string
    {
        $this->addData($data);

        return $this->init()->render(
            $this->index,
            $this->getProperties()
        );
    }

    public function removeScripts(): void
    {
        $this->scripts = [];
    }

    public function removeStyles(): void
    {
        $this->styles = [];
    }

    /**
     * Private methods
     */

    private function init(): Environment
    {
        $twigLoader = new FilesystemLoader($this->directory);
        return new Environment($twigLoader, []);
    }

    private function addExtension(string $filename): string
    {
        return preg_replace(['/^\/*/', '/.twig$/'], '', $filename) . ".twig";
    }
}
