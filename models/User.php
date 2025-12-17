<?php
class User {
    public $id;
    public $email;
    public $password;
    public $role;
    public $createdAt;
    
    public function __construct($email, $password, $role = 'user') {
        $this->id = uniqid('user_');
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->role = $role;
        $this->createdAt = date('Y-m-d H:i:s');
    }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
            'createdAt' => $this->createdAt
        ];
    }
    
    public static function fromArray($data) {
        $user = new User($data['email'], '');
        $user->id = $data['id'];
        $user->password = $data['password'];
        $user->role = $data['role'];
        $user->createdAt = $data['createdAt'];
        return $user;
    }
}
?>