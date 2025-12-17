<?php
require_once 'config.php';

class UserService {
    private array $users = [];           
    private ?User $currentUser = null;   
    
    public function __construct() {
        $this->loadUsers();
    }
    
    private function loadUsers(): void {  
        $data = readData(USERS_FILE);
        foreach ($data as $userData) {
            $this->users[$userData['id']] = User::fromArray($userData);
        }
    }
    
    private function saveUsers(): void {  
        $data = [];
        foreach ($this->users as $user) {
            $data[] = $user->toArray();
        }
        writeData(USERS_FILE, $data);
    }
    
    public function register(string $email, string $password, string $role = 'user'): User|false {  // ДОБАВЛЕН ТИП: string для параметров, User|false для возврата
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
    
    public function login(string $email, string $password): User|false {  
        foreach ($this->users as $user) {
            if ($user->email === $email && password_verify($password, $user->password)) {
                $this->currentUser = $user;
                return $user;
            }
        }
        return false;
    }
    
    public function logout(): void {  
        $this->currentUser = null;
    }
    
    public function getCurrentUser(): ?User {  
        return $this->currentUser;
    }
    
    public function getUserById(string $id): ?User {  
        return isset($this->users[$id]) ? $this->users[$id] : null;
    }
    
    public function getUserByEmail(string $email): ?User { 
        foreach ($this->users as $user) {
            if ($user->email === $email) {
                return $user;
            }
        }
        return null;
    }
    
    public function getAllUsers(): array {  
        return array_values($this->users);
    }
}
?>