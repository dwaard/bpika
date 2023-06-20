<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Notifications extends Component
{

    public $hasNotifications = true;

    public ?string $type = null;

    public string $message = '';

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        if (session()->has('success')) {
            $this->type = 'success';
            $this->message = session()->get('success');
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.notifications');
    }
}
