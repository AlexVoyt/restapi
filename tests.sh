#! /bin/bash
url=http://localhost/restapi/public/index.php

SendRequest()
{
    method=$1
    route=$2
    json=$3

    curl -X $method $url$route\
         -H 'Content-Type: application/json'\
         -d $json
    printf "\n";
}

SendRequest POST /user/ '{"login":"user","password":"user"}'
SendRequest POST /user/ '{"login":"user2","password":"user2"}'
SendRequest GET /todo/ '{"login":"user","password":"user"}'
SendRequest DELETE /todo/1 '{"login":"user2","password":"user2"}'
SendRequest DELETE /todo/1 '{"login":"user","password":"user"}'
SendRequest PUT /todo/5 '{"login":"user","password":"user","description":"updated_description_2"}'
# SendRequest POST /todo/ '{"login":"user","password":"user","description":"Haha todo thingy goes brrrrr"}'
