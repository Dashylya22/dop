<?php
class Project {
    public $id;
    public $name;
    public $description;
    public $ownerId;
    public $createdAt;
    
    public function __construct($name, $description, $ownerId) {
        $this->id = uniqid('project_');
        $this->name = $name;
        $this->description = $description;
        $this->ownerId = $ownerId;
        $this->createdAt = date('Y-m-d H:i:s');
    }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'ownerId' => $this->ownerId,
            'createdAt' => $this->createdAt
        ];
    }
    
    public static function fromArray($data) {
        $project = new Project($data['name'], $data['description'], $data['ownerId']);
        $project->id = $data['id'];
        $project->createdAt = $data['createdAt'];
        return $project;
    }
}
?>