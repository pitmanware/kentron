<?php

    namespace Kentron\Proxy;

    use Kentron\Facade\Twig;

    class Page
    {
        public  $properties = [
                    "meta" => [
                        "title" => "",
                    ],
                    "script" => [
                        "js" => [ "main.js" ],
                        "css" => []
                    ],
                    "frame_view" => "index.twig",
                    "template" => "/Shared/default.twig",
                    "data" => []
                ];

        private $twig = null;

        public function __construct (array $pageSettings = [])
        {
            $twigDir = APP_DIR . "View/";

            $this->twig = new Twig($twigDir);

            // Set the default values from the config
            if (count($pageSettings) > 0) {
                $this->setDefaults($pageSettings);
            }
        }

        /**
         *
         * Setters
         *
         */

        final public function setScripts (array $scripts): void
        {
            $this->properties["script"]["js"] = $scripts;
        }

        final public function setStyles (array $styles): void
        {
            $this->properties["script"]["css"] = $styles;
        }

        final public function setTemplate (string $templatePath): void
        {
            $this->properties["template"] = $templatePath;
        }

        final public function setTitle (string $title): void
        {
            $this->properties["meta"]["title"] = $title;
        }

        final public function setFrameView (string $framePath): void
        {
            $this->properties["frame_view"] = $framePath;
        }

        /**
         *
         * Helper functions
         *
         */

        final public function removeScripts (): void
        {
            $this->setScripts([]);
            $this->setStyles([]);
        }

        final public function addScript (string $scriptPath): void
        {
            $this->properties["script"]["js"][] = $scriptPath;
        }

        final public function addScripts (array $scripts): void
        {
            foreach ($scripts as $script) {
                $this->addScript($script);
            }
        }

        final public function addStyle (string $stylePath): void
        {
            $this->properties["script"]["css"][] = $stylePath;
        }

        final public function addStyles (array $styles): void
        {
            foreach ($styles as $style) {
                $this->addStyle($style);
            }
        }

        final public function render (): void
        {
            $this->twig->renderView($this->properties["frame_view"], $this->properties);
        }

        final public function capture (): string
        {
            return $this->twig->captureView($this->properties["frame_view"], $this->properties["data"]);
        }

        /**
         *
         * Private functions
         *
         */

        private function setDefaults (array $pageSettings): void
        {
            $title          = $pageSettings["title"] ?? "";
            $defaultScripts = $pageSettings["scripts"] ?? [];
            $defaultStyles  = $pageSettings["styles"] ?? [];

            $this->setTitle($title);
            $this->addScripts($defaultScripts);
            $this->addStyles($defaultStyles);
        }
    }
