<?php

namespace App\View\Components;

use Closure;
use Fuzzy\Fzpkg\Traits\JsonObjectTrait;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Http\Request;

class NavBar extends Component
{
    use JsonObjectTrait;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    // --- HTMX

    public function getNavBarHtmx(Request $request)
    {
        return view('components.commons.nav-bar');
    }

    // ---

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.commonms.nav-bar');
    }
}
