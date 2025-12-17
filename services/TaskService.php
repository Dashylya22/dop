<?php
require_once 'config.php';

class TaskService {
    private $tasks = [];
    private $projectService;
    private $userService;
    
    public function __construct($projectService, $userService) {
        $this->projectService = $projectService;
        $this->userService = $userService;
        $this->loadTasks();
    }
    
    private function loadTasks() {
        $data = readData(TASKS_FILE);
        foreach ($data as $taskData) {
            $this->tasks[$taskData['id']] = Task::fromArray($taskData);
        }
    }
    
    private function saveTasks() {
        $data = [];
        foreach ($this->tasks as $task) {
            $data[] = $task->toArray();
        }
        writeData(TASKS_FILE, $data);
    }
    
    public function createTask($projectId, $title, $description) {
        $project = $this->projectService->getProjectById($projectId);
        if (!$project) {
            return false;
        }
        
        $task = new Task($title, $description, $projectId);
        $this->tasks[$task->id] = $task;
        $this->saveTasks();
        
        return $task;
    }
    
    public function getTaskById($id) {
        return isset($this->tasks[$id]) ? $this->tasks[$id] : null;
    }
    
    public function getAllTasks() {
        return array_values($this->tasks);
    }
    
    public function updateTask($taskId, $title, $description, $priority) {
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
    
    public function deleteTask($taskId) {
        if (!isset($this->tasks[$taskId])) {
            return false;
        }
        
        unset($this->tasks[$taskId]);
        $this->saveTasks();
        return true;
    }
    
    public function assignTask($taskId, $userId) {
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
    
    public function changeStatus($taskId, $status) {
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
    
    public function filterTasks($status = null, $assigneeId = null, $priority = null) {
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
    
    public function getProjectTasks($projectId) {
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