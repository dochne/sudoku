<?php
include("vendor/autoload.php");
chdir(__DIR__);

$implementations = [];
$directories = scandir(__DIR__);
foreach ($directories as $folder) {
    $filename = $folder . "/metadata.json";
    if (is_dir($folder) && file_exists($filename)) {
        $data = json_decode(file_get_contents($filename), true);
        if (!isset($data["language"])) {
            throw new \Exception($filename . " does not contain a 'language' section");
        }

        if (!isset($data["implementations"])) {
            throw new \Exception($filename . " does not contain a 'implementations' section");
        }

        ["language" => $language, "implementations" => $languageImplementations] = $data;
        foreach ($languageImplementations as $implementationName => $implementationCommand) {
            $implementations[] = [$language, $folder, $implementationName, $implementationCommand];
        }
    }
}

echo "Running Benchmarks\n";
foreach ($implementations as $index => [$language, $folder, $implementationName, $implementationCommand]) {
    echo "Running {$language}/{$implementationName}\n";
    $cacheFilename = __DIR__ . "/cache/".$folder . "-" . str_replace(" ", "", $implementationName);

    if (!file_exists($cacheFilename)) {
        chdir($folder);
        $output = "";
        exec($implementationCommand, $output);
        file_put_contents($cacheFilename, implode("\n", $output));
        chdir(__DIR__);
    }

    $implementations[$index][] = json_decode(file_get_contents($cacheFilename), true);
}

// Order by fastest to slowest
usort($implementations, function($arr1, $arr2) {
    return $arr1[4]["time"] - $arr2[4]["time"];
});


$output = new \Symfony\Component\Console\Output\StreamOutput(STDOUT);
$table = new \Symfony\Component\Console\Helper\Table($output);

$table->setHeaders(["Language", "ImplementationName", "Benchmark"]);
foreach ($implementations as $implementation) {
    $table->addRow([$implementation[0], $implementation[2], $implementation[4]["time"]]);
}
$table->render();
echo "\n";