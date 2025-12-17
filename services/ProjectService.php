<?php
require_once 'config.php';

class ProjectService {
    private $projects = [];
    private $userService;
    
    public function __construct($userService) {
        $this->userService = $userService;
        $this->loadProjects();
    }
    
    private function loadProjects() {
        $data = readData(PROJECTS_FILE);
        foreach ($data as $projectData) {
            $this->projects[$projectData['id']] = Project::fromArray($projectData);
        }
    }
    
    private function saveProjects() {
        $data = [];
        foreach ($this->projects as $project) {
            $data[] = $project->toArray();
        }
        writeData(PROJECTS_FILE, $data);
    }
    
    public function createProject($name, $description) {
        $user = $this->userService->getCurrentUser();
        if (!$user) {
            return false;
        }
        
        $project = new Project($name, $description, $user->id);
        $this->projects[$project->id] = $project;
        $this->saveProjects();
        
        return $project;
    }
    
    public function getUserProjects() {
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
    
    public function getAllProjects() {
        return array_values($this->projects);
    }
    
    public function getProjectById($id) {
        return isset($this->projects[$id]) ? $this->projects[$id] : null;
    }
    
    public function deleteProject($projectId) {
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