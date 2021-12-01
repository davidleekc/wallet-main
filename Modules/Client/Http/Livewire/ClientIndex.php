<?php

namespace Modules\Client\Http\Livewire;

use Modules\Client\Entities\Client;
use Livewire\Component;
use Livewire\WithPagination;

class ClientsIndex extends Component
{
    use WithPagination;
    public $searchTerm;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $searchTerm = '%' . $this->searchTerm . '%';
        $clients = Client::where('phone', 'like', $searchTerm)->orWhere('pin', 'like', $searchTerm)->paginate();

        return view('livewire.clients-index', compact('clients'));
    }
}