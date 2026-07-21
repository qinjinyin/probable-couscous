<?php
echo 'SCRIPT_FILENAME: ' . ($_SERVER['SCRIPT_FILENAME'] ?? 'NOT SET') . "\n";
echo 'APP_PATH: ' . (dirname($_SERVER['SCRIPT_FILENAME']) . DIRECTORY_SEPARATOR) . "\n";
echo 'ROOT_PATH: ' . (dirname(realpath(dirname($_SERVER['SCRIPT_FILENAME']) . DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR) . "\n";
