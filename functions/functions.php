<?php

function sanitizeInput(string $input): string
{
    return htmlspecialchars(trim($input));
}

function validateEmail(string $email): string
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword(string $password): string
{
    return strlen($password) >= 6;
}
