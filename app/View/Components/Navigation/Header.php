<?php

namespace App\View\Components\Navigation;

use App\View\MenuComposer;
use Illuminate\View\Component;

class Header extends Component
{
    public $headerMenuItems;

    public function __construct(MenuComposer $menuComposer)
    {
        $this->headerMenuItems = $menuComposer->getMenuBySetting('menu.header');
    }

    public function render()
    {
        return view('components.navigation.header');
    }
}
