<?php
namespace App\utils;

use Symfony\Component\HttpFoundation\Request;

// Multiple return values? Out parameters?? Return array???
function EnsureJsonAndConvertToArray(Request $req)
{
}

// accepts incoming json in associative array format
function EnsureUserCredentials($data)
{
    $login_provided = isset($data['login']);
    $password_provided = isset($data['password']);
    if(!$login_provided || !$password_provided)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function ExtractUserCredentials()
{

}