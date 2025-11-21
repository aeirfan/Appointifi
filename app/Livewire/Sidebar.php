<?php

namespace App\Livewire;

use Livewire\Component;

class Sidebar extends Component
{
    public $isOpen = true; // Changed to true to be open by default on mobile
    public $darkMode = false;

    protected $listeners = [
        'toggle-sidebar' => 'toggleSidebar'
    ];

    public function mount()
    {
        // Initialize dark mode from session or default to false
        $this->darkMode = session('dark_mode', false);
        // Default to open on desktop
        $this->isOpen = true;
    }

    public function toggleSidebar()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function toggleDarkMode()
    {
        $this->darkMode = !$this->darkMode;
        session(['dark_mode' => $this->darkMode]);

        // Dispatch event with the correct format for Alpine.js
        $this->dispatch('dark-mode-toggled', darkMode: $this->darkMode);
    }

    public function render()
    {
        return view('livewire.sidebar');
    }
}
