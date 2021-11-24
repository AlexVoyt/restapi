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
    return (isset($data['login']) && isset($data['password']));
}

function ExtractUserCredentials()
{
}
