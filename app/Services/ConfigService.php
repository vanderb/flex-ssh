<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ConfigService
{

    protected $disk;

    protected $aliases;

    public function __construct()
    {
        $this->disk = app('ssh_disk');
    }

    public function getAliases(): Collection
    {
        $this->aliases = collect();
        $aliases = explode("\n\n", $this->disk->get('ssh_alias'));
        $aliases = array_filter($aliases);

        if (!empty($aliases)) {
            foreach ($aliases as $alias) {
                $alias = explode(PHP_EOL, $alias);
                $this->sshLine = collect();
                array_map(function ($l) {
                    if ($trimmed = trim($l)) {
                        list($key, $value) = explode(' ', $trimmed);
                        $this->sshLine->put($key, $value);
                        return $trimmed;
                    }
                }, $alias);
                $this->aliases->push($this->sshLine);
            }
        }

        return $this->aliases;
    }

    public function find($alias)
    {
        return $this->getAliases()->firstWhere('Host', $alias);
    }

    public function findByHost($host)
    {
        return $this->getAliases()->where('HostName', $host);
    }

    public function aliasExists(string $alias): bool
    {
        return !is_null($this->getAliases()->firstWhere('Host', $alias));
    }

    public function createNewAlias(Collection $aliasConfig)
    {
        $aliases = $this->getAliases();

        $aliases->push($aliasConfig);

        return $this->saveAliasList($aliases);
    }

    public function delete($alias)
    {
        $aliases = $this->getAliases();

        $aliases = $aliases->reject(function ($item) use ($alias) {
            return $item['Host'] == $alias;
        });

        return $this->saveAliasList($aliases);
    }

    public function update($aliasName, $updatedAlias)
    {
        $aliases = $this->getAliases();

        $index = $aliases->search(function ($alias) use ($aliasName) {
            return $alias->get('Host') == $aliasName;
        });

        $aliases->replace($index, $updatedAlias);

        return $this->saveAliasList($aliases);
    }

    protected function saveAliasList($aliases)
    {
        $aliases = $aliases->sortBy('Host')->map(function ($alias) {
            return "Host {$alias->get('Host')}\n" . "  HostName {$alias->get('HostName')}\n" . "  User {$alias->get('User')}\n" . "  Port {$alias->get('Port', 22)}\n";
        });

        return $this->disk->put('ssh_alias', implode("\n", $aliases->toArray()));
    }
}
