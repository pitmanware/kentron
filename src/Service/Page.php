<?php

    namespace Kentron\Service;

    use Kentron\Facade\Twig;

    final class Page
    {
        private $baseDirectory;
        private $data;
        private $frame;
        private $meta;
        private $scripts;
        private $styles;
        private $template;

        public function __construct (?string $directory = null)
        {
            $this->scripts = new \stdClass();
            $this->styles  = new \stdClass();
            $this->meta    = new \stdClass();

            // Set any defaults
            $this->setFrame("index.twig");
            $this->setTitle("");

            if (is_string($directory)) {
                $this->setDirectory($directory);
            }
        }

        /**
         * Setters
         */

        /**
         * Set the base directory for Twig
         * @param  string $directory The path of the directory to be used
         * @throws InvalidArgumentException
         */
        public function setDirectory (string $directory): void
        {
            if (file_exists($directory) !== false && is_dir($directory) !== false) {
                throw new \InvalidArgumentException("$directory is not a valid directory");
            }

            $this->baseDirectory = $directory;
        }

        public function addJs ($js): void
        {
            if (is_null($this->scripts->js)) {
                $this->scripts->js = [];
            }

            if (is_array($js)) {
                $this->scripts->js = array_merge($this->scripts->js, $js);
            }
            else if (is_string($js) && file_exists($js)) {
                $this->scripts->js[] = $js;
            }
        }

        public function addCss ($styles): void
        {
            $this->properties["script"]["css"] = $styles;
        }

        public function setTemplate (string $templatePath): void
        {
            $this->template = $templatePath;
        }

        public function setTitle (string $title): void
        {
            $this->meta->title = $title;
        }

        public function setFrame (string $framePath): void
        {
            $this->frame = $framePath;
        }

        /**
         * Helper methods
         */

        public function removeScripts (): void
        {
            $this->scripts = [];
        }

        public function removeStyles (): void
        {
            $this->styles = [];
        }

        public function addScript (string $scriptPath): void
        {
            $this->scripts += $scriptPath;
        }

        public function addScripts (array $scripts): void
        {
            foreach ($scripts as $script) {
                $this->addScript($script);
            }
        }

        public function addStyle (string $stylePath): void
        {
            $this->styles += $stylePath;
        }

        public function addStyles (array $styles): void
        {
            foreach ($styles as $style) {
                $this->addStyle($style);
            }
        }

        public function render (): void
        {
            $twig = new Twig($this->baseDirectory);
            $twig->renderView($this->frame, $this->getProperties());
        }

        public function capture (): string
        {
            $twig = new Twig($this->baseDirectory);
            return $twig->captureView($this->properties["frame_view"], $this->properties["data"]);
        }

        private function getProperties (): array
        {
            return [
                "meta" => [
                    "title" => $this->title,
                ],
                "script" => [
                    "js" => $this->scripts,
                    "css" => $this->styles
                ],
                "frame_view" => $this->frame,
                "template" => $this->template,
                "data" => $this->data
            ];
        }
    }
