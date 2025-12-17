<?php
// Загружаем файлы напрямую с правильными именами
require_once __DIR__ . '/config.php';

// Загружаем модели
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Project.php';
require_once __DIR__ . '/models/Task.php';

// Загружаем сервисы - исправляем имя файла
require_once __DIR__ . '/services/UserService.php';  // Используем текущее имя файла
require_once __DIR__ . '/services/ProjectService.php';
require_once __DIR__ . '/services/TaskService.php';

// Инициализация сервисов
$userService = new UserService();
$projectService = new ProjectService($userService);
$taskService = new TaskService($projectService, $userService);

// Создаем тестового пользователя если нет пользователей
if (empty($userService->getAllUsers())) {
    $userService->register('admin@test.com', 'admin123', 'admin');
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Создан тестовый пользователь: admin@test.com / admin123\n";
    echo str_repeat("=", 50) . "\n";
}

// Главное меню
function showMainMenu(UserService $userService, ProjectService $projectService, TaskService $taskService): void {    
    $currentUser = $userService->getCurrentUser();
    
    while (true) {
        echo "\n" . str_repeat("=", 50);
        echo "\n=== СИСТЕМА УПРАВЛЕНИЯ ЗАДАЧАМИ ===\n";
        echo "Текущий пользователь: " . ($currentUser ? $currentUser->email : "Не авторизован");
        echo "\n" . str_repeat("=", 50) . "\n";
        
        if (!$currentUser) {
            echo "1. Войти\n";
            echo "2. Зарегистрироваться\n";
        } else {
            echo "1. Проекты\n";
            echo "2. Задачи\n";
            echo "3. Пользователи\n";
            echo "4. Выйти\n";
        }
        echo "0. Выход\n";
        echo str_repeat("-", 50) . "\n";
        echo "Выберите действие: ";
        
        $choice = trim(fgets(STDIN));
        
        if (!$currentUser) {
            switch ($choice) {
                case '1':
                    showLoginForm($userService);
                    $currentUser = $userService->getCurrentUser();
                    break;
                case '2':
                    showRegisterForm($userService);
                    break;
                case '0':
                    echo "До свидания!\n";
                    exit;
                default:
                    echo "Неверный выбор!\n";
            }
        } else {
            switch ($choice) {
                case '1':
                    showProjectMenu($projectService, $taskService);
                    break;
                case '2':
                    showTaskMenu($taskService, $projectService, $userService);
                    break;
                case '3':
                    showUserMenu($userService);
                    break;
                case '4':
                    $userService->logout();
                    $currentUser = null;
                    echo "Вы вышли из системы.\n";
                    break;
                case '0':
                    echo "До свидания!\n";
                    exit;
                default:
                    echo "Неверный выбор!\n";
            }
        }
    }
}

function showLoginForm(UserService $userService): void {   
    echo "\n=== ВХОД В СИСТЕМУ ===\n";
    echo "Email: ";
    $email = trim(fgets(STDIN));
    echo "Пароль: ";
    $password = trim(fgets(STDIN));
    
    $user = $userService->login($email, $password);
    if ($user) {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Успешный вход! Добро пожаловать, " . $user->email . "\n";
        echo str_repeat("=", 50) . "\n";
    } else {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Ошибка входа! Проверьте email и пароль.\n";
        echo str_repeat("=", 50) . "\n";
    }
}

function showRegisterForm(UserService $userService): void {  
    echo "\n=== РЕГИСТРАЦИЯ ===\n";
    echo "Email: ";
    $email = trim(fgets(STDIN));
    echo "Пароль: ";
    $password = trim(fgets(STDIN));
    
    $user = $userService->register($email, $password);
    if ($user) {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Регистрация успешна! Теперь войдите в систему.\n";
        echo str_repeat("=", 50) . "\n";
    } else {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Ошибка регистрации! Пользователь с таким email уже существует.\n";
        echo str_repeat("=", 50) . "\n";
    }
}

function showProjectMenu(ProjectService $projectService, TaskService $taskService): void {     
    while (true) {
        echo "\n=== УПРАВЛЕНИЕ ПРОЕКТАМИ ===\n";
        echo "1. Создать проект\n";
        echo "2. Мои проекты\n";
        echo "3. Все проекты\n";
        echo "4. Просмотр проекта с задачами\n";
        echo "5. Удалить проект\n";
        echo "0. Назад\n";
        echo "Выберите действие: ";
        
        $choice = trim(fgets(STDIN));
        
        switch ($choice) {
            case '1':
                echo "Название проекта: ";
                $name = trim(fgets(STDIN));
                echo "Описание: ";
                $description = trim(fgets(STDIN));
                
                $project = $projectService->createProject($name, $description);
                if ($project) {
                    echo "\n" . str_repeat("=", 50) . "\n";
                    echo "Проект создан: " . $project->name . "\n";
                    echo str_repeat("=", 50) . "\n";
                } else {
                    echo "\n" . str_repeat("=", 50) . "\n";
                    echo "Ошибка создания проекта!\n";
                    echo str_repeat("=", 50) . "\n";
                }
                break;
                
            case '2':
                $projects = $projectService->getUserProjects();
                echo "\n=== ВАШИ ПРОЕКТЫ ===\n";
                if (empty($projects)) {
                    echo "Нет проектов\n";
                } else {
                    foreach ($projects as $project) {
                        echo "ID: " . $project->id . "\n";
                        echo "Название: " . $project->name . "\n";
                        echo "Описание: " . $project->description . "\n";
                        echo "Дата создания: " . $project->createdAt . "\n";
                        echo str_repeat("-", 30) . "\n";
                    }
                }
                break;
                
            case '3':
                $projects = $projectService->getAllProjects();
                echo "\n=== ВСЕ ПРОЕКТЫ ===\n";
                if (empty($projects)) {
                    echo "Нет проектов\n";
                } else {
                    foreach ($projects as $project) {
                        echo "ID: " . $project->id . "\n";
                        echo "Название: " . $project->name . "\n";
                        echo "Дата создания: " . $project->createdAt . "\n";
                        echo str_repeat("-", 30) . "\n";
                    }
                }
                break;
                
            case '4':
                echo "ID проекта: ";
                $projectId = trim(fgets(STDIN));
                $project = $projectService->getProjectById($projectId);
                
                if ($project) {
                    echo "\n=== ПРОЕКТ: " . $project->name . " ===\n";
                    echo "Описание: " . $project->description . "\n";
                    echo "Дата создания: " . $project->createdAt . "\n\n";
                    
                    // Получаем задачи проекта
                    $tasks = $taskService->getAllTasks();
                    $projectTasks = [];
                    foreach ($tasks as $task) {
                        if ($task->projectId === $projectId) {
                            $projectTasks[] = $task;
                        }
                    }
                    
                    echo "Задачи проекта (" . count($projectTasks) . "):\n";
                    
                    if (empty($projectTasks)) {
                        echo "Нет задач\n";
                    } else {
                        foreach ($projectTasks as $task) {
                            echo "• " . $task->title . " [" . $task->status . "]\n";
                        }
                    }
                } else {
                    echo "\n" . str_repeat("=", 50) . "\n";
                    echo "Проект не найден!\n";
                    echo str_repeat("=", 50) . "\n";
                }
                break;
                
            case '5':
                echo "ID проекта для удаления: ";
                $projectId = trim(fgets(STDIN));
                
                if ($projectService->deleteProject($projectId)) {
                    echo "\n" . str_repeat("=", 50) . "\n";
                    echo "Проект удален!\n";
                    echo str_repeat("=", 50) . "\n";
                } else {
                    echo "\n" . str_repeat("=", 50) . "\n";
                    echo "Ошибка удаления! Проверьте ID проекта и права доступа.\n";
                    echo str_repeat("=", 50) . "\n";
                }
                break;
                
            case '0':
                return;
                
            default:
                echo "Неверный выбор!\n";
        }
    }
}

function showTaskMenu(TaskService $taskService, ProjectService $projectService, UserService $userService): void {     
    while (true) {
        echo "\n=== УПРАВЛЕНИЕ ЗАДАЧАМИ ===\n";
        echo "1. Создать задачу\n";
        echo "2. Все задачи\n";
        echo "3. Назначить исполнителя\n";
        echo "4. Изменить статус\n";
        echo "5. Фильтрация задач\n";
        echo "0. Назад\n";
        echo "Выберите действие: ";
        
        $choice = trim(fgets(STDIN));
        
        switch ($choice) {
            case '1':
                echo "ID проекта: ";
                $projectId = trim(fgets(STDIN));
                echo "Название задачи: ";
                $title = trim(fgets(STDIN));
                echo "Описание: ";
                $description = trim(fgets(STDIN));
                
                $task = $taskService->createTask($projectId, $title, $description);
                if ($task) {
                    echo "\n" . str_repeat("=", 50) . "\n";
                    echo "Задача создана: " . $task->title . "\n";
                    echo str_repeat("=", 50) . "\n";
                } else {
                    echo "\n" . str_repeat("=", 50) . "\n";
                    echo "Ошибка создания задачи!\n";
                    echo str_repeat("=", 50) . "\n";
                }
                break;
                
            case '2':
                $tasks = $taskService->getAllTasks();
                echo "\n=== ВСЕ ЗАДАЧИ ===\n";
                if (empty($tasks)) {
                    echo "Нет задач\n";
                } else {
                    foreach ($tasks as $task) {
                        echo "ID: " . $task->id . "\n";
                        echo "Название: " . $task->title . "\n";
                        echo "Статус: " . $task->status . "\n";
                        echo "Приоритет: " . $task->priority . "\n";
                        $project = $projectService->getProjectById($task->projectId);
                        echo "Проект: " . ($project ? $project->name : "Неизвестно") . "\n";
                        echo str_repeat("-", 30) . "\n";
                    }
                }
                break;
                
            case '3':
                echo "ID задачи: ";
                $taskId = trim(fgets(STDIN));
                echo "Email исполнителя: ";
                $email = trim(fgets(STDIN));
                
                $user = $userService->getUserByEmail($email);
                if ($user && $taskService->assignTask($taskId, $user->id)) {
                    echo "\n" . str_repeat("=", 50) . "\n";
                    echo "Исполнитель назначен!\n";
                    echo str_repeat("=", 50) . "\n";
                } else {
                    echo "\n" . str_repeat("=", 50) . "\n";
                    echo "Ошибка назначения исполнителя!\n";
                    echo str_repeat("=", 50) . "\n";
                }
                break;
                
            case '4':
                echo "ID задачи: ";
                $taskId = trim(fgets(STDIN));
                echo "Новый статус (new, in_progress, done): ";
                $status = trim(fgets(STDIN));
                
                if ($taskService->changeStatus($taskId, $status)) {
                    echo "\n" . str_repeat("=", 50) . "\n";
                    echo "Статус изменен!\n";
                    echo str_repeat("=", 50) . "\n";
                } else {
                    echo "\n" . str_repeat("=", 50) . "\n";
                    echo "Ошибка изменения статуса!\n";
                    echo str_repeat("=", 50) . "\n";
                }
                break;
                
            case '5':
                echo "\n=== ФИЛЬТРАЦИЯ ЗАДАЧ ===\n";
                echo "Статус (оставьте пустым если не нужно): ";
                $status = trim(fgets(STDIN));
                $status = $status ?: null;
                
                echo "Email исполнителя (оставьте пустым если не нужно): ";
                $email = trim(fgets(STDIN));
                $assigneeId = null;
                if ($email) {
                    $user = $userService->getUserByEmail($email);
                    $assigneeId = $user ? $user->id : null;
                }
                
                echo "Приоритет (low, medium, high) (оставьте пустым если не нужно): ";
                $priority = trim(fgets(STDIN));
                $priority = $priority ?: null;
                
                $tasks = $taskService->filterTasks($status, $assigneeId, $priority);
                echo "\nНайдено задач: " . count($tasks) . "\n";
                
                foreach ($tasks as $task) {
                    echo "• " . $task->title . " [" . $task->status . ", " . $task->priority . "]\n";
                }
                break;
                
            case '0':
                return;
                
            default:
                echo "Неверный выбор!\n";
        }
    }
}

function showUserMenu(UserService $userService): void {   
    while (true) {
        echo "\n=== УПРАВЛЕНИЕ ПОЛЬЗОВАТЕЛЯМИ ===\n";
        echo "1. Все пользователи\n";
        echo "2. Информация о текущем пользователе\n";
        echo "0. Назад\n";
        echo "Выберите действие: ";
        
        $choice = trim(fgets(STDIN));
        
        switch ($choice) {
            case '1':
                $users = $userService->getAllUsers();
                echo "\n=== ВСЕ ПОЛЬЗОВАТЕЛИ ===\n";
                foreach ($users as $user) {
                    echo "Email: " . $user->email . "\n";
                    echo "Роль: " . $user->role . "\n";
                    echo "Дата регистрации: " . $user->createdAt . "\n";
                    echo str_repeat("-", 30) . "\n";
                }
                break;
                
            case '2':
                $user = $userService->getCurrentUser();
                if ($user) {
                    echo "\n=== ТЕКУЩИЙ ПОЛЬЗОВАТЕЛЬ ===\n";
                    echo "Email: " . $user->email . "\n";
                    echo "Роль: " . $user->role . "\n";
                    echo "Дата регистрации: " . $user->createdAt . "\n";
                }
                break;
                
            case '0':
                return;
                
            default:
                echo "Неверный выбор!\n";
        }
    }
}

// Запуск приложения
showMainMenu($userService, $projectService, $taskService);
?>