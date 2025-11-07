<?php

declare(strict_types=1);

namespace Megio\Translation\Parser;

use Nette\Neon\Exception;
use Nette\Neon\Neon;

use function array_merge;
use function file_exists;
use function is_array;

class NeonParser
{
    /**
     * @throws Exception
     *
     * @return array<string, string>
     */
    public function parseFileToFlatten(string $path): array
    {
        if (file_exists($path) === false) {
            return [];
        }

        $neon = Neon::decodeFile($path);

        if (is_array($neon) === false) {
            return [];
        }

        return $this->flattenNeon($neon);
    }

    /**
     * Converts nested NEON array into flattened array with dot notation keys.
     * user: { form: { email: { label: 'Email' }}}
     * → 'user.form.email.label' => 'Email'
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    private function flattenNeon(
        array $data,
        string $prefix = '',
    ): array {
        $result = [];

        foreach ($data as $key => $value) {
            $newKey = $prefix === '' ? $key : "{$prefix}.{$key}";

            if (is_array($value) === true) {
                $result = array_merge($result, $this->flattenNeon($value, $newKey));
            } else {
                $result[$newKey] = (string)$value;
            }
        }

        return $result;
    }
}
