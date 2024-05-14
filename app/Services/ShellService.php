<?php

namespace App\Services;

use App\Events\MyCustomEvent;
use App\Models\Connection;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;

class ShellService
{
    public const COMMAND_ALIASES = [
        'll' => 'ls -l'
    ];
    public const DISALLOWED_COMMANDS = [
        'rm'
    ];

    public SSH2 $ssh;
    protected string $output = '';
    protected string $displayName;
    protected string $error = '';

    public static function getConnections(): array
    {
        return Connection::all()->pluck('id', 'name')->toArray();
    }

    public static function connectionExists(): bool
    {
        $connectName = self::getConnection();

        $connection = Connection::where('name', $connectName)->first();

        return ! empty($connection);
    }

    public static function setConnection(string $connectName): void
    {
        Session::put('connection', $connectName);
    }

    public static function getConnection(): string
    {
        return Session::get('connection');
    }

    public static function getConnectionId(): ?int
    {
        $connectName = self::getConnection();

        $connection = Connection::where('name', $connectName)->first();

        return $connection ? $connection->id : null;
    }

    public static function getConnectionName(): string
    {
        $connectName = self::getConnection();

        return $connectName ?: 'Unknown';
    }


    public function connect(string $connectName): bool
    {
        $connection = Connection::where('name', $connectName)->first();

        if ($connection === null) {
            return false;
        }

        $key =  $connection->key_path;
        if (! file_exists($key)) {
            return false;
        }

        try {
            $key = PublicKeyLoader::load(file_get_contents($key), $connection->pass_phrase);
            $this->ssh = new SSH2($connection->host);
            $this->ssh->setTimeout(10);
            $this->ssh->login($connection->user, $key);
        } catch (Exception $e) {
            $this->error = $e->getMessage();

            return false;
        }

        $this->displayName = $connection->host . '@' . $connection->user;

        return true;
    }

    public function testConnection(string $host, string $username, string $keyPath, string $passPhrase): ?string
    {
        if (! file_exists($keyPath)) {
            return null;
        }

        try {
            $key = PublicKeyLoader::load(file_get_contents($keyPath), $passPhrase);
            $ssh = new SSH2($host);
            $ssh->login($username, $key);
            return $ssh->read('username@username:~$');
        } catch (Exception $e) {
            $this->error = $e->getMessage();

            return false;
        }
    }

    public function execute(string $command): bool|string
    {
        $command = self::COMMAND_ALIASES[$command] ?? $command;

        if ($this->isDisallowed($command)) {
            return '"' . $command . '" contains a disallowed command.';
        }

        return $this->ssh->exec($command);
    }

    public function getError()
    {
        return $this->error;
    }

    protected function isDisallowed(string $command): bool
    {
        $disallowed = false;

        collect(explode(' ', $command))
            ->each(function ($word) use (&$disallowed) {
                if (in_array($word, ShellService::DISALLOWED_COMMANDS)) {
                    $disallowed = true;
                }
            });

        return $disallowed;
    }
}

