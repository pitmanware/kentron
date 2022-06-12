<?php
declare(strict_types=1);

namespace Kentron\Template\Http\Controller;

use Kentron\Entity\TransportEntity;
use Kentron\Facade\View;

/**
 * Abstract extension of the base controller for API routes
 */
abstract class ASystemController extends AController
{
    public function __construct(TransportEntity $transportEntity)
    {
        $transportEntity->setQuiet();

        parent::__construct($transportEntity);
    }

    protected function renderErrors(?string $directory = null, ?string $index = null, ?string $frame = null): void
    {
        $view = new View($directory, $index, $frame);

        $this->transportEntity->setHtml();

        $view->removeScripts();
        $view->setAlerts($this->transportEntity->normaliseAlerts());

        $view->render();
    }
}
