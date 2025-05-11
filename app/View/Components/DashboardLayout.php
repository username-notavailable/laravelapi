<?php

namespace App\View\Components;

use Closure;
use Fuzzy\Fzpkg\Traits\JsonObjectTrait;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DashboardLayout extends Component
{
    use JsonObjectTrait;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $dashboardTitle = 'Dashboard',
        public string $dashboardSubTitle = '',
        public string $title = '',
        public array $breadcrumbLinks = []
    )
    {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.dashboard-layout');
    }
}
