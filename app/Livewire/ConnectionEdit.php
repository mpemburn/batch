<?php

namespace App\Livewire;

use App\Models\Connection;
use App\Services\ConnectionService;
use App\Services\ShellService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ConnectionEdit extends Component
{
    public int $connectionId = 0;
    public string $name = '';
    public string $host = '';
    public string $user = '';
    public string $keyPath = '';
    public string $passPhrase = '';
    public string $output = '';
    public bool $showDelete = false;
    public bool $showNewButton = true;
    public bool $loading = false;

    public function editConnection(): void
    {
        if ($this->connectionId === 0) {
            return;
        }

        $this->output = '';

        $connection = Connection::find($this->connectionId);

        if (!$connection) {
            $this->name = 'No Connection selected.';
            return;
        }
        $this->name = $connection->name;
        $this->host = $connection->host;
        $this->user = $connection->user;
        $this->keyPath = $connection->key_path;
        $this->passPhrase = $connection->pass_phrase;

        $this->showDelete = true;
    }
    public function test(): void
    {
        $this->loading = true;
        $this->output = 'Testing...';

        $service = new ShellService();
        $output = $service->testConnection(
            $this->host,
            $this->user,
            $this->keyPath,
            $this->passPhrase
        );

        if ($output) {
            $this->output = $output;
        } else {
            $this->output = "Connection to \"{$this->name}\" failed: \n" . $service->getError();
        }

        $this->loading = false;
    }

    public function save(): void
    {
        if ($this->connectionId === 0) {
            $this->saveNewConnection();
            return;
        }

        $connection = Connection::find($this->connectionId);

        $connection->update([
            'name' => $this->name,
            'host' => $this->host,
            'user' => $this->user,
            'key_path' => $this->keyPath,
            'pass_phrase' => $this->passPhrase,
        ]);
    }

    public function newConnection(): void
    {
        $this->showNewButton = false;
        $this->showDelete = false;
        $this->connectionId = 0;
        $this->clearFields();
        $this->dispatch('focusName');
    }

    public function saveNewConnection(): void
    {
        Connection::create([
            'name' => $this->name,
            'host' => $this->host,
            'user' => $this->user,
            'key_path' => $this->keyPath,
            'pass_phrase' => $this->passPhrase,
        ]);

        $this->showNewButton = true;
    }

    public function delete(): void
    {
        $connection = Connection::find($this->connectionId);
        $connection->delete();
        $this->clearFields();
    }

    public function clear(): void
    {
        $this->connectionId = 0;
        $this->clearFields();
        $this->output = '';
        $this->showDelete = false;
        $this->showNewButton = true;
    }

    protected function clearFields(): void
    {
        $this->name = '';
        $this->host = '';
        $this->user = '';
        $this->keyPath = '';
        $this->passPhrase = '';
        $this->output = '';
    }

    public function render()
    {
        $connections = (new ConnectionService())->getConnectionList();

        return view('livewire.connection-crud', ['connections' => $connections]);
    }
}
