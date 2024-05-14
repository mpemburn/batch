<?php

namespace App\Batches;

use Illuminate\Support\Collection;

class PluginBatch extends Batch
{
    protected array $commands = [
        '<div data-plugin="{{ item }}">',
        'cd {{ env }}',
        'wp plugin deactivate --network --skip-plugins --skip-themes {{ item }}',
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
            if (in_array($itemParts[0], ['N', 'UN'])) {
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

}
