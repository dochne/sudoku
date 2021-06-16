<?php
include("vendor/autoload.php");
chdir(__DIR__);

//$exampleInputFile = realpath(__DIR__ . "/examples/3_input.txt");
//$exampleOutputFile = realpath(__DIR__ . "/examples/3_output.txt");
//$exampleOutput = str_replace(["\n", " ", "\t"], "", file_get_contents($exampleOutputFile));
$examples = [];
foreach (["1", "2", "3", "4", "5"] as $id) {
    $inputFile = realpath(__DIR__ . "/examples/{$id}_input.txt");
    $outputFile = realpath(__DIR__ . "/examples/{$id}_output.txt");

    $examples[$id] = [
        "input" => $inputFile,
        //"outputFile" => $outputFile,
        "output" => file_exists($outputFile) ?
            str_replace(["\n", " ", "\t"], "", file_get_contents($outputFile))
            : null
    ];
}


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
            if (!($implementationConfig["enabled"] ?? true)) {
                continue;
            }

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


    foreach ($examples as $id => $example) {
        echo "Running {$language}/{$name} Example {$id}\n";
        $cacheFilename = __DIR__ . "/cache/".$folder . "-" . str_replace(" ", "", $name) . "-{$id}";

        if (!file_exists($cacheFilename) || $noCache) {
            chdir($folder);
            if (isset($config["dir"])) {
                if (!file_exists($config["dir"])) {
                    throw new \Exception("Folder " . $config["dir"] . " does not exist");
                }
                chdir($config["dir"]);
            }
            if (isset($config["build"])) {
                echo "Running build step: {$config["build"]}\n";
                exec($config["build"]);
            }

            $start = microtime(true);
            $output = "";
            $command = $config["run"]. " ". $example["input"];
            echo "Running {$command}\n";
            exec($command, $output);
            $taken = microtime(true) - $start;
            $selfReportedTime = null;

            $result = implode("\n", $output);

            if (($result[0] ?? "") === "{") {
                $decoded = json_decode($result, true);
                $selfReportedTime = $decoded["time"];
                $result = $decoded["output"];
            }

            $valid = true;
            if ($example["output"] !== null) {
                $valid = ($example["output"] === str_replace(["\n", " ", "\t"], "", $result));
            }

            //$valid = true;
            file_put_contents($cacheFilename, json_encode(["time" => $taken, "result" => $result, "valid" => $valid, "self-time" => $selfReportedTime], JSON_PRETTY_PRINT));
            chdir(__DIR__);
        }

        $implementations[$index]["result"][$id] = json_decode(file_get_contents($cacheFilename), true);
    }
}

//print_r($implementations);
foreach ($implementations as $id => $implementation) {
    $valid = true;
    $timings = [];
    $selfTimings = [];
    foreach ($implementation["result"] as $exampleName => $result) {
        if (!$result["valid"]) {
            $valid = false;
        }
        $timings[] = $result["time"];
        if (isset($result["self-time"])) {
            $selfTimings[] = $result["self-time"];
        }
    }

    $implementations[$id]["average-result"] = ["time" => array_sum($timings) / count($timings), "valid" => $valid];
    if (count($selfTimings) > 0) {
        $implementations[$id]["average-self-result"] = ["time" => array_sum($selfTimings) / count($selfTimings), "valid" => $valid];
    }
}

// Order by fastest to slowest
usort($implementations, function($arr1, $arr2) {
    return $arr2["average-result"]["time"] < $arr1["average-result"]["time"];
});

//$includeResult = false;

$headers = ["Language", "ImplementationName"];
$headers[] = "Average";
foreach ($examples as $id => $example) {
    $headers[] = "Example " . $id;
}
//
//if ($includeResult) {
//    $headers[] = "Result";
//}
$rows = [];
$selfReportedRows = [];
foreach ($implementations as $implementation) {
    $row = [
        $implementation["language"],
        $implementation["name"]
    ];
    $selfReportedRow = $row;
    $selfReported = isset($implementation["result"][1]["self-time"]);

    $row[] = !$implementation["average-result"]["valid"] ? "\u{2718}" : round($implementation["average-result"]["time"], 6);
    if ($selfReported) {
        $selfReportedRow[] = !$implementation["average-self-result"]["valid"] ? "\u{2718}" : round($implementation["average-self-result"]["time"], 6);
    }

    foreach ($examples as $id => $example) {
        $col = !$implementation["result"][$id]["valid"] ? "\u{2718}" : round($implementation["result"][$id]["time"], 6);
        $selfCol = !$implementation["result"][$id]["valid"] ? "\u{2718}" : round($implementation["result"][$id]["self-time"], 6);

//        $includeResult = true;
//        if ($includeResult) {
//            $col .= "\n" . $implementation["result"][$id]["result"];
//        }

        $selfReportedRow[] = $selfCol;
        $row[] = $col;
    }

    if ($selfReported) {
        $selfReportedRows[] = $selfReportedRow;
    }

//        $implementation["result"]["valid"] ?
//        round($implementation["result"]["time"], 6),
//        $implementation["result"]["valid"] ? "\u{2713}" : "\u{2718}"
//    ];

    //$implementation["result"]["valid"] ? "\u{2713}" : "\u{2718}"
//
//    if ($includeResult) {
//        $row[] = "-----------------\n" . $implementation["result"]["result"];
//    }
    $rows[] = $row;
}

$green = "\033[32m";
$none = "\033[0m";
$lowest = [];
foreach ($selfReportedRows as $k => $row) {
    foreach ($row as $col => $value) {
        if ($col <= 1) {
            continue;
        }
        if (!isset($lowest[$col]) || $lowest[$col] > $value) {
            $lowest[$col] = $value;
        }
    }
}

foreach ($selfReportedRows as $k => $row) {
    foreach ($row as $col => $value) {
        if ($col <= 1) {
            continue;
        }
        if ($value === $lowest[$col]) {
            $selfReportedRows[$k][$col] = $green . $value . $none;
        }
    }
}



echo "Script reported times:\n";
$output = new \Symfony\Component\Console\Output\StreamOutput(STDOUT);
$table = new \Symfony\Component\Console\Helper\Table($output);
$table->setHeaders($headers);
$table->addRows($rows);
$table->render();
echo "\n";

usort($selfReportedRows, function($arr1, $arr2) {
    return $arr2[2] < $arr1[2];
});

echo "Self-reported times:\n";
$output = new \Symfony\Component\Console\Output\StreamOutput(STDOUT);
$table = new \Symfony\Component\Console\Helper\Table($output);
$table->setHeaders($headers);
$table->addRows($selfReportedRows);
$table->render();

$file = "# Benchmarks\n";
$file .= "|" . implode("|", $headers) . "|\n";
$file .= "|" . implode("|", array_map(function($v) {return "---";}, $headers)) . "|\n";
foreach ($rows as $row) {
    $file .= "|" . implode("|", $row) . "|\n";
}

$file .= "\n";
$file .= "# Self Reported Benchmarks\n";
$file .= "|" . implode("|", $headers) . "|\n";
$file .= "|" . implode("|", array_map(function($v) {return "---";}, $headers)) . "|\n";
foreach ($selfReportedRows as $row) {
    $file .= "|" . implode("|", $row) . "|\n";
}

file_put_contents("benchmark.md", $file);

