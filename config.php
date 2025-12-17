<?php
// Автозагрузка классов
spl_autoload_register(function (string $class): void {  
    $paths = ['models/', 'services/'];
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// Константы
define('DATA_DIR', 'data/');
define('USERS_FILE', DATA_DIR . 'users.json');
define('PROJECTS_FILE', DATA_DIR . 'projects.json');
define('TASKS_FILE', DATA_DIR . 'tasks.json');

// Создаем директории если их нет
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}

// Инициализация файлов данных
function initDataFiles(): void {  
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
function readData(string $file): array {  
    if (!file_exists($file)) {
        return [];
    }
    $content = file_get_contents($file);
    return json_decode($content, true) ?: [];
}

// Запись данных в JSON файл
function writeData(string $file, array $data): void {  
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

// Очистка ввода
function cleanInput(string $input): string {  
    return htmlspecialchars(strip_tags(trim($input)));
}

// Вывод сообщения
function showMessage(string $message): void { 
    echo "\n" . str_repeat("=", 50) . "\n";
    echo $message . "\n";
    echo str_repeat("=", 50) . "\n";
}

// Инициализируем файлы данных
initDataFiles();
?>