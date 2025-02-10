<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Tutorials extends Component
{
    public $layout;

    protected $listeners = [
        'update_daily_target' => 'update_daily_target',
    ];

    public function construct()
    {
        if (!auth()->check() || !in_array(auth()->user()->is_employee, [0, 2, 4, 6])) {
            return false;
        }

        $layout_map = [
            0 => 'layouts.app-sale',
            2 => 'layouts.app-admin',
            4 => 'layouts.app-support',
            6 => 'layouts.app-manager',
        ];

        $this->layout = $layout_map[auth()->user()->is_employee];

        return true;
    }

    public function mount()
    {
        if (!$this->construct()) {
            return redirect()->route('login');
        }
    }

    public function render()
    {

        return view('livewire.tutorials')->extends($this->layout);
    }
}
