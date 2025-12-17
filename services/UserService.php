<?php
require_once 'config.php';

class UserService {
    private $users = [];
    private $currentUser = null;
    
    public function __construct() {
        $this->loadUsers();
    }
    
    private function loadUsers() {
        $data = readData(USERS_FILE);
        foreach ($data as $userData) {
            $this->users[$userData['id']] = User::fromArray($userData);
        }
    }
    
    private function saveUsers() {
        $data = [];
        foreach ($this->users as $user) {
            $data[] = $user->toArray();
        }
        writeData(USERS_FILE, $data);
    }
    
    public function register($email, $password, $role = 'user') {
        // Проверяем существует ли пользователь
        foreach ($this->users as $user) {
            if ($user->email === $email) {
                return false;
            }
        }
        
        $user = new User($email, $password, $role);
        $this->users[$user->id] = $user;
        $this->saveUsers();
        
        return $user;
    }
    
    public function login($email, $password) {
        foreach ($this->users as $user) {
            if ($user->email === $email && password_verify($password, $user->password)) {
                $this->currentUser = $user;
                return $user;
            }
        }
        return false;
    }
    
    public function logout() {
        $this->currentUser = null;
    }
    
    public function getCurrentUser() {
        return $this->currentUser;
    }
    
    public function getUserById($id) {
        return isset($this->users[$id]) ? $this->users[$id] : null;
    }
    
    public function getUserByEmail($email) {
        foreach ($this->users as $user) {
            if ($user->email === $email) {
                return $user;
            }
        }
        return null;
    }
    
    public function getAllUsers() {
        return array_values($this->users);
    }
}
?>