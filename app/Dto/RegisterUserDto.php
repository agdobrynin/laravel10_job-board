<?php

namespace App\Dto;

readonly class RegisterUserDto
{
    public function __construct(
        public string  $email,
        // user name
        public string  $name,
        public string  $password,
        // for register user as employer
        public bool    $is_employer = false,
        public ?string $employer_name = null,
    )
    {
    }
}
