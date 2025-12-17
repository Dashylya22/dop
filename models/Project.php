<?php
class Project {
    public string $id;           
    public string $name;         
    public string $description;  
    public string $ownerId;     
    public string $createdAt;    
    
    public function __construct(string $name, string $description, string $ownerId) {  
        $this->id = uniqid('project_');
        $this->name = $name;
        $this->description = $description;
        $this->ownerId = $ownerId;
        $this->createdAt = date('Y-m-d H:i:s');
    }
    
    public function toArray(): array {  
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'ownerId' => $this->ownerId,
            'createdAt' => $this->createdAt
        ];
    }
    
    public static function fromArray(array $data): Project { 
        $project = new Project($data['name'], $data['description'], $data['ownerId']);
        $project->id = $data['id'];
        $project->createdAt = $data['createdAt'];
        return $project;
    }
}
?>