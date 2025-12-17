<?php
// Автозагрузка классов
spl_autoload_register(function ($class) {
    // Пути относительно текущего файла
    $paths = [
        __DIR__ . '/models/',
        __DIR__ . '/services/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Константы с абсолютными путями
define('DATA_DIR', __DIR__ . '/data/');
define('USERS_FILE', DATA_DIR . 'users.json');
define('PROJECTS_FILE', DATA_DIR . 'projects.json');
define('TASKS_FILE', DATA_DIR . 'tasks.json');

// Создаем директории если их нет
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}

// Инициализация файлов данных
function initDataFiles() {
    $files = [
        USERS_FILE => [],
        PROJECTS_FILE => [],
        TASKS_FILE => []
    ];
    
    foreach ($files as $file => $defaultData) {
        if (!file_exists($file)) {
            file_put_contents($file, json_encode($defaultData, JSON_PRETTY_PRINT));
        }
    }
}

// Чтение данных из JSON файла
function readData($file) {
    if (!file_exists($file)) {
        return [];
    }
    $content = file_get_contents($file);
    return json_decode($content, true) ?: [];
}

// Запись данных в JSON файл
function writeData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

// Очистка ввода
function cleanInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Вывод сообщения
function showMessage($message) {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo $message . "\n";
    echo str_repeat("=", 50) . "\n";
}

// Инициализируем файлы данных
initDataFiles();
?>