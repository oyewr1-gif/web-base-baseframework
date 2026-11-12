<?php
class AuthModule extends CoreModule {
    protected $endpoint = '/auth';

    public function login($username, $password) {
        return ApiClient::request('POST', "{$this->endpoint}/login", [
            'username' => $username,
            'password' => $password
        ], false);
    }

    public function register($username, $email, $password) {
        return ApiClient::request('POST', "{$this->endpoint}/register", [
            'username' => $username,
            'email'    => $email,
            'password' => $password
        ], false);
    }
}
