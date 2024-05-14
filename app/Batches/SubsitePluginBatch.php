<?php

namespace App\Batches;

use App\Models\ActiveOnSite;
use Illuminate\Support\Collection;
use phpseclib3\Math\BigInteger\Engines\PHP;

class SubsitePluginBatch extends Batch
{
    protected array $commands = [
        '<div data-plugin="{{ item }}">',
        'cd {{ env }}',
        '{{ repeat }}wp plugin deactivate {{ item }} --url={{ url }}',
        'wp site create --skip-plugins --skip-themes --slug=rem-{{ item }} --title="Removed {{ item }}"',
        '# <a href="https://www.clone.clarku.edu/rem-{{ item }}" target="_blank" >https://www.clone.clarku.edu/rem-{{ item }}</a>',
        '</div>'
    ];

    protected function filter(): Collection
    {
        return $this->batch->map(function ($item) {
            if (str_starts_with($item,'conn') || str_starts_with($item,'dir')) {
                return $item;
            }
            $itemParts = explode(' ', $item);
            if (in_array($itemParts[0], ['A', 'I', 'UI'])) {
                return $item;
            }

            return null;
        });
    }

    protected function callback(string $item): ?string
    {
        $itemParts = explode(' ', $item);

        return strtolower($itemParts[1]);
    }

    protected function repeatCommand(string $item, string $command): ?string
    {
        $plugin = preg_replace('/([\w]+)( )([\w-]+)(.*)/', '$3', $item);
        $command = str_replace('{{ repeat }}', '', $command);
        $commands = collect();
        ActiveOnSite::where('plugin', $plugin)
            ->get()
            ->each(function (ActiveOnSite $activeOnSite) use (&$commands, $command) {
                $commands->push(str_replace('{{ url }}', trim($activeOnSite->url), $command));
            });

        return $commands->isNotEmpty() ? $commands->implode(';' . PHP_EOL) : null;
    }
}
