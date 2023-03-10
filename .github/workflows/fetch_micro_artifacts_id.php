<?php
$arch = [
    'macos.arm' => '592209492',
    'macos.x86_64' => '592209493',
];
$id = $arch[getenv('ARCH')];
echo "::set-output name=id::" . $id . PHP_EOL;
