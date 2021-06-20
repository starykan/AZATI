<?php

declare(strict_types=1);

function mb_count_chars(string $input): array
{
    $unique = [];
    for($i = 0; $i < mb_strlen($input, 'UTF-8'); $i++) {
        $char = mb_substr($input, $i, 1, 'UTF-8');
        if(!array_key_exists($char, $unique)) {
            $unique[$char] = 0;
        }
        $unique[$char]++;
    }

    return $unique;
}

$array = [ 'rfv', 'vfr', 'abc', 'bac', 'dbatre', 'qwer', 'cba', 'terbda' ];

$result = [];
while (!empty($array)) {
    $currentWord = array_shift($array);
    $newGroup = [$currentWord];
    $letters = mb_count_chars($currentWord);
    foreach ($array as $key => $word) {
        if ($letters == mb_count_chars($word)) {
            $newGroup[] = $word;
            unset($array[$key]);
        }
    }
    $result[] = $newGroup;
}
var_dump($result);
