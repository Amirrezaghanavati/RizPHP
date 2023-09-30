<?php

namespace System\View\Traits;

use mysql_xdevapi\Exception;

trait HasViewLoader
{
    private array $viewNameArray = [];

    private function viewLoader($dir): string
    {
        $dir = trim($dir, ' .');
        $dir = str_replace('.', '/', $dir);

        if (file_exists(dirname(__DIR__, 3) . "/resources/view/$dir.blade.php")) {
            $this->registerView($dir);
            return htmlentities(file_get_contents(dirname(__DIR__, 3) . "/resources/view/$dir.blade.php"));
        }

        throw new Exception('view not found');
    }

    private function registerView($view): void
    {
        $this->viewNameArray[] = $view;
    }
}