<?php

namespace System\View;

use System\View\Traits\HasViewLoader;
use System\View\Traits\HasExtendsContent;
use Exception;
use System\View\Traits\HasIncludeContent;
use App\Providers\AppServiceProvider;

class ViewBuilder{
    use HasViewLoader,HasExtendsContent,HasIncludeContent;

    public $content;
    public array $vars = [];

    public function run($dir): void
    {
        $this->content = $this->viewLoader($dir);
        $this->checkExtendsContent();
        $this->checkIncludesContent();
        Composer::setViews($this->viewNameArray);
        $this->vars = Composer::getVars();

    }
}