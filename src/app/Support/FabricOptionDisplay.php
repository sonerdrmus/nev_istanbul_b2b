<?php

namespace App\Support;

/**
 * Mağaza kumaş varyasyonu etiketlerini (örn. "28/1 Compact Penye — 200-210 gr/m²") parçalar.
 */
final class FabricOptionDisplay
{
    /**
     * @return array{yarn_count: ?string, name: string, weight: ?string, full: string}
     */
    public static function parse(string $label): array
    {
        $full = trim($label);
        $working = $full;
        $weight = null;

        if (preg_match('/^(.*?)\s+[—–-]\s*(gr\/m².+)$/iu', $working, $matches)) {
            $working = trim($matches[1]);
            $weight = trim($matches[2]);
        } elseif (preg_match('/^(.*?)\s+[—–-]\s*(.+)$/u', $working, $matches)) {
            $working = trim($matches[1]);
            $weight = trim($matches[2]);
        }

        $yarnCount = null;
        $name = $working;

        if (preg_match('/^(\d+\/\d+)\s+(.+)$/u', $working, $matches)) {
            $yarnCount = $matches[1];
            $name = trim($matches[2]);
        }

        return [
            'yarn_count' => $yarnCount,
            'name' => $name !== '' ? $name : $full,
            'weight' => $weight,
            'full' => $full,
        ];
    }
}
