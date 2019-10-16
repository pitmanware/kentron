<?php

    namespace Kentron\Facade;

    use Twig\Loader\FilesystemLoader;
    use Twig\Environment;

    final class Twig
    {
        private $twig;

        public function __construct (string $twigDir)
        {
            $twigLoader = new FilesystemLoader($twigDir);
            $this->twig = new Environment($twigLoader, []);
        }

        public function renderView (string $view, $data = []): void
        {
            echo $this->captureView($view, $data);
        }

        public function captureView (string $view, array $data = []): string
        {
            return $this->twig->render($view, $data);
        }
    }
