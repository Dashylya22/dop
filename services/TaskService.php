<?php
require_once 'config.php';

class TaskService {
    private array $tasks = [];            
    private ProjectService $projectService;  
    private UserService $userService;         
    
    public function __construct(ProjectService $projectService, UserService $userService) {  
        $this->projectService = $projectService;
        $this->userService = $userService;
        $this->loadTasks();
    }
    
    private function loadTasks(): void {  
        $data = readData(TASKS_FILE);
        foreach ($data as $taskData) {
            $this->tasks[$taskData['id']] = Task::fromArray($taskData);
        }
    }
    
    private function saveTasks(): void {   
        $data = [];
        foreach ($this->tasks as $task) {
            $data[] = $task->toArray();
        }
        writeData(TASKS_FILE, $data);
    }
    
    public function createTask(string $projectId, string $title, string $description): Task|false {  // ДОБАВЛЕН ТИП: string для параметров, Task|false для возврата
        $project = $this->projectService->getProjectById($projectId);
        if (!$project) {
            return false;
        }
        
        $task = new Task($title, $description, $projectId);
        $this->tasks[$task->id] = $task;
        $this->saveTasks();
        
        return $task;
    }
    
    public function getTaskById(string $id): ?Task {   
        return isset($this->tasks[$id]) ? $this->tasks[$id] : null;
    }
    
    public function getAllTasks(): array {   
        return array_values($this->tasks);
    }
    
    public function updateTask(string $taskId, string $title, string $description, string $priority): bool {  // ДОБАВЛЕН ТИП: string для параметров, bool для возврата
        $task = $this->getTaskById($taskId);
        if (!$task) {
            return false;
        }
        
        $task->title = $title;
        $task->description = $description;
        $task->priority = $priority;
        $task->updatedAt = date('Y-m-d H:i:s');
        
        $this->saveTasks();
        return true;
    }
    
    public function deleteTask(string $taskId): bool {    
        if (!isset($this->tasks[$taskId])) {
            return false;
        }
        
        unset($this->tasks[$taskId]);
        $this->saveTasks();
        return true;
    }
    
    public function assignTask(string $taskId, string $userId): bool {     
        $task = $this->getTaskById($taskId);
        $user = $this->userService->getUserById($userId);
        
        if (!$task || !$user) {
            return false;
        }
        
        $task->assigneeId = $userId;
        $task->updatedAt = date('Y-m-d H:i:s');
        $this->saveTasks();
        
        return true;
    }
    
    public function changeStatus(string $taskId, string $status): bool {     
        $task = $this->getTaskById($taskId);
        if (!$task) {
            return false;
        }
        
        $validStatuses = ['new', 'in_progress', 'done'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $task->status = $status;
        $task->updatedAt = date('Y-m-d H:i:s');
        $this->saveTasks();
        
        return true;
    }
    
    public function filterTasks(?string $status = null, ?string $assigneeId = null, ?string $priority = null): array {  // ДОБАВЛЕН ТИП: ?string для параметров, array для возврата
        $filtered = [];
        
        foreach ($this->tasks as $task) {
            $match = true;
            
            if ($status && $task->status !== $status) {
                $match = false;
            }
            
            if ($assigneeId && $task->assigneeId !== $assigneeId) {
                $match = false;
            }
            
            if ($priority && $task->priority !== $priority) {
                $match = false;
            }
            
            if ($match) {
                $filtered[] = $task;
            }
        }
        
        return $filtered;
    }
    
    public function getProjectTasks(string $projectId): array {   
        $projectTasks = [];
        foreach ($this->tasks as $task) {
            if ($task->projectId === $projectId) {
                $projectTasks[] = $task;
            }
        }
        return $projectTasks;
    }
}
?>