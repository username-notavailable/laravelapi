<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Fuzzy\Fzpkg\Traits\JsonObjectTrait;

class MainLayout extends Component
{
    use JsonObjectTrait;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public bool $includeMetaEnv = true,
        public bool $includeFavicon = true,
        public bool $includeAppCss = true,
        public bool $includeAppJs = true,
        public bool $includeJsTranslations = true,
        public bool $includeToastContainer = true,
        public bool $includeSizer = true,

        public string $pageMetas = '',
        public string $pageCss = '',
        public string $pageInlineCss = '',
        public string $pageJs = '',
        public string $pageInlineJs = '',

        public bool $useLoadingWaitSpinner = true,
        public bool $dumpSession = true,
        public bool $runInitJsPage = true,

        public string $title = '',
        public string $bodyClasses = '',
        public string $bodyStyle = ''
    )
    {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.main-layout');
    }
}
