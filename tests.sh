#! /bin/bash
url=https://restapi.alexvoyt.com

SendRequest()
{
    method=$1
    route=$2
    json=$3

    printf "Curl request: ";
    echo curl -X $method $url$route\
         -H 'Content-Type: application/json'\
         -d $json

    printf "Response: ";
    curl -X $method $url$route\
         -H 'Content-Type: application/json'\
         -d $json
    printf "\n\n";
}

SendRequest POST /user/ '{"login":"user","password":"user"}'
SendRequest POST /user/ '{"login":"user2","password":"user2"}'
SendRequest POST /user/ '{"login":"user3","password":"user3"}'
SendRequest GET /todo/ '{"login":"user","password":"user"}'
SendRequest PUT /todo/2 '{"login":"user","password":"user","description":"updated_description_2"}'
SendRequest POST /todo/ '{"login":"user","password":"user","description":"Haha_todo_thingy_goes_brrrrr2"}'
SendRequest DELETE /todo/1 '{"login":"user","password":"user"}'
SendRequest DELETE /todo/132 '{"login":"user","password":"user"}'
