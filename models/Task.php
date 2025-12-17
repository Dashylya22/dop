<?php
class Task {
    public $id;
    public $title;
    public $description;
    public $status;
    public $priority;
    public $projectId;
    public $assigneeId;
    public $createdAt;
    public $updatedAt;
    
    public function __construct($title, $description, $projectId) {
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
    
    public function toArray() {
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
    
    public static function fromArray($data) {
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