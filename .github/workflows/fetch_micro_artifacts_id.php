<?php
$arch = [
    'macos.arm' => '592209492',
    'macos.x86_64' => '592209493',
    'linux.musl.aarch64' => '593883918',
    'linux.musl.x86_64' => '593883919',
    'linux.glibc.x86_64' => '593883910',
    'windows.x86_64' => '593809854'
];
$id = $arch[getenv('ARCH')];
echo "::set-output name=id::" . $id . PHP_EOL;
