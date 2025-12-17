<?php
class Task {
    public string $id;         
    public string $title;      
    public string $description; 
    public string $status;       
    public string $priority;     
    public string $projectId;    
    public ?string $assigneeId;  
    public string $createdAt;    
    public string $updatedAt;    
    
    public function __construct(string $title, string $description, string $projectId) {  
        $this->id = uniqid('task_');
        $this->title = $title;
        $this->description = $description;
        $this->status = 'new';
        $this->priority = 'medium';
        $this->projectId = $projectId;
        $this->assigneeId = null;  
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
    }
    
    public function toArray(): array { 
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'projectId' => $this->projectId,
            'assigneeId' => $this->assigneeId,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
    
    public static function fromArray(array $data): Task {  
        $task = new Task($data['title'], $data['description'], $data['projectId']);
        $task->id = $data['id'];
        $task->status = $data['status'];
        $task->priority = $data['priority'];
        $task->assigneeId = $data['assigneeId'];
        $task->createdAt = $data['createdAt'];
        $task->updatedAt = $data['updatedAt'];
        return $task;
    }
}
?>