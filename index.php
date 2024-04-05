<?php

const NUMBER_OF_PASSWORDS_TO_GENERATE = 5;

$passwords = [];

if (!empty($_POST['no_words']) && is_numeric($_POST['no_words']) && $_POST['no_words'] > 0) {
    for ($x = 0; $x < NUMBER_OF_PASSWORDS_TO_GENERATE; $x++) {
        $password = '';

        $no_words = (int)$_POST['no_words'];
        $words    = getRandomWords($no_words);

        foreach ($words as $y => $word) {
            $capitalization_type = $_POST['capitalization_type'] ?? 'words';
            if ($capitalization_type === 'none') {
                $password .= $word;
            } else if ($capitalization_type === 'alternating') {
                $password .= $y % 2 === 0 ? strtolower($word) : strtoupper($word);
            } else if ($capitalization_type === 'all') {
                $password .= strtoupper($word);
            } else if ($capitalization_type === 'words') {
                $password .= ucfirst($word);
            }
        }

        if (!empty($_POST['add_number']) && $_POST['add_number'] === 'on') {
            $disallowed_numbers = [33, 66, 69];
            $random_number      = rand(1, 99);

            while (in_array($random_number, $disallowed_numbers)) {
                $random_number = rand(1, 99);
            }

            $password .= str_pad($random_number, 2, '0', STR_PAD_LEFT);
        }

        if (!empty($_POST['add_symbol']) && $_POST['add_symbol'] === 'on') {
            $symbols  = ['!', '@', '#', '$', '%', '^', '&', '*'];
            $password .= $symbols[rand(0, count($symbols) - 1)];
        }

        $passwords[] = $password;
    }
}

function getRandomWords(int $number_of_words): array {
    $words = [];
    $file  = fopen('english-common-words.txt', 'r');
    while (!feof($file)) {
        $words[] = trim(fgets($file));
    }
    fclose($file);

    $random_words = [];
    for ($x = 0; $x < $number_of_words; $x++) {
        $random_words[] = $words[rand(0, count($words) - 1)];
    }

    return $random_words;
}

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Password Generator</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
    <body class="bg-body-secondary">
        <div class="container">
            <div class="bg-body p-3 rounded mt-5">
                <h1>Password Generator</h1>

                <hr>

                <form action="" method="post">
                    <div class="mb-3">
                        <label for="no_words">Number of words</label>
                        <input type="number" id="no_words" class="form-control" value="<?= $_POST['no_words'] ?? 3 ?>" name="no_words" min="1" max="10" required>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="add_number" name="add_number" <?= ($_POST['add_number'] ?? null) === 'on' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="add_number">Add number</label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="add_symbol" name="add_symbol" <?= ($_POST['add_symbol'] ?? null) === 'on' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="add_symbol">Add symbol</label>
                    </div>

                    <div class="mb-3">
                        <p class="m-0">Capitalization</p>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="capitalization_type_none" name="capitalization_type" value="none" <?= ($_POST['capitalization_type'] ?? null) === 'none' ? 'checked' : '' ?>>
                            <label for="capitalization_type_none">None</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="capitalization_type_alternating" name="capitalization_type" value="alternating" <?= ($_POST['capitalization_type'] ?? null) === 'alternating' ? 'checked' : '' ?>>
                            <label for="capitalization_type_alternating">Alternating</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="capitalization_type_all" name="capitalization_type" value="all" <?= ($_POST['capitalization_type'] ?? null) === 'all' ? 'checked' : '' ?>>
                            <label for="capitalization_type_all">All</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="capitalization_type_words" name="capitalization_type" value="words" <?= ($_POST['capitalization_type'] ?? 'words') === 'words' ? 'checked' : '' ?>>
                            <label for="capitalization_type_words">Words</label>
                        </div>
                    </div>

                    <button class="btn btn-sm btn-primary" type="submit">Generate</button>
                </form>

                <div class="card mt-3">
                    <div class="card-body">
                        <div class="row fw-bold">
                            <div class="col-1">Action</div>
                            <div class="col">Password</div>
                            <div class="col text-end">Length</div>
                        </div>
                        <?php foreach ($passwords as $password) { ?>
                            <hr class="my-1">
                            <div class="row">
                                <div class="col-1 d-flex align-items-center">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('<?= $password ?>')">Copy</button>
                                </div>
                                <div class="col d-flex align-items-center"><?= $password ?></div>
                                <div class="col d-flex align-items-center justify-content-end"><?= strlen($password) ?></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

            </div>

        </div>
    </body>

    <script>
        function copyToClipboard(content) {
            navigator.clipboard.writeText(content).then(() => {
                alert('Copied to clipboard')
            })
        }
    </script>
</html>