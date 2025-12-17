<?php
require_once 'config.php';

class ProjectService {
    private array $projects = []; 
    private UserService $userService;  
    
    public function __construct(UserService $userService) { 
        $this->userService = $userService;
        $this->loadProjects();
    }
    
    private function loadProjects(): void {  
        $data = readData(PROJECTS_FILE);
        foreach ($data as $projectData) {
            $this->projects[$projectData['id']] = Project::fromArray($projectData);
        }
    }
    
    private function saveProjects(): void {  
        $data = [];
        foreach ($this->projects as $project) {
            $data[] = $project->toArray();
        }
        writeData(PROJECTS_FILE, $data);
    }
    
    public function createProject(string $name, string $description): Project|false {  
        $user = $this->userService->getCurrentUser();
        if (!$user) {
            return false;
        }
        
        $project = new Project($name, $description, $user->id);
        $this->projects[$project->id] = $project;
        $this->saveProjects();
        
        return $project;
    }
    
    public function getUserProjects(): array {  
        $user = $this->userService->getCurrentUser();
        if (!$user) {
            return [];
        }
        
        $userProjects = [];
        foreach ($this->projects as $project) {
            if ($project->ownerId === $user->id) {
                $userProjects[] = $project;
            }
        }
        
        return $userProjects;
    }
    
    public function getAllProjects(): array {  
        return array_values($this->projects);
    }
    
    public function getProjectById(string $id): ?Project {  
        return isset($this->projects[$id]) ? $this->projects[$id] : null;
    }
    
    public function deleteProject(string $projectId): bool {  
        $project = $this->getProjectById($projectId);
        if (!$project) {
            return false;
        }
        
        $user = $this->userService->getCurrentUser();
        if ($project->ownerId !== $user->id) {
            return false;
        }
        
        unset($this->projects[$projectId]);
        $this->saveProjects();
        
        return true;
    }
}
?>