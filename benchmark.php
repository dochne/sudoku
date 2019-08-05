<?php
include("vendor/autoload.php");
chdir(__DIR__);

$exampleInputFile = realpath(__DIR__ . "/examples/2_input.txt");
$exampleOutputFile = realpath(__DIR__ . "/examples/2_output.txt");
$exampleOutput = str_replace(["\n", " ", "\t"], "", file_get_contents($exampleOutputFile));

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
        foreach ($languageImplementations as $implementationName => $implementationConfig) {
            $implementations[] = [
                "language" => $language,
                "folder" => $folder,
                "name" => $implementationName,
                "config" => $implementationConfig
            ];
        }
    }
}

$noCache = ($argv[1] ?? "") ==="--no-cache";

if ($noCache) {
    echo "Cache disabled!\n";
}
echo "Running Benchmarks\n";
foreach ($implementations as $index => ["language" => $language, "folder" => $folder, "name" => $name, "config" => $config]) {
    echo "Running {$language}/{$name}\n";
    $cacheFilename = __DIR__ . "/cache/".$folder . "-" . str_replace(" ", "", $name);

    if (!file_exists($cacheFilename) || $noCache) {
        chdir($folder);
        if (isset($config["build"])) {
            echo "Running build step: {$config["build"]}\n";
            exec($config["build"]);
        }

        $start = microtime(true);
        $output = "";
        $command = $config["run"]. " ". $exampleInputFile;
        echo "Running {$command}\n";
        exec($command, $output);
        $taken = microtime(true) - $start;
        $result = implode("\n", $output);
        $valid = ($exampleOutput === str_replace(["\n", " ", "\t"], "", $result));

        file_put_contents($cacheFilename, json_encode(["time" => $taken, "result" => $result, "valid" => $valid], JSON_PRETTY_PRINT));
        chdir(__DIR__);
    }

    $implementations[$index]["result"] = json_decode(file_get_contents($cacheFilename), true);
}

// Order by fastest to slowest
usort($implementations, function($arr1, $arr2) {
    return $arr1["result"]["time"] - $arr2["result"]["time"];
});


$includeResult = false;

$headers = ["Language", "ImplementationName", "Benchmark", "Valid"];
if ($includeResult) {
    $headers[] = "Result";
}
$rows = [];
foreach ($implementations as $implementation) {
    $row = [
        $implementation["language"],
        $implementation["name"],
        $implementation["result"]["time"],
        $implementation["result"]["valid"] ? "\u{2713}" : "\u{2718}"
    ];

    if ($includeResult) {
        $row[] = "-----------------\n" . $implementation["result"]["result"];
    }
    $rows[] = $row;
}


$output = new \Symfony\Component\Console\Output\StreamOutput(STDOUT);
$table = new \Symfony\Component\Console\Helper\Table($output);
$table->setHeaders($headers);
$table->addRows($rows);
$table->render();
echo "\n";

$file = "# Benchmarks\n";
$file .= "----\n";
$file .= implode("|", $headers) . "\n";
$file .= "----\n";
foreach ($rows as $row) {
    $file .= implode("|", $row) . "\n";
}
$file .= "---\n";
file_put_contents("BENCHMARK.md", $file);

