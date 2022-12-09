<?php

namespace App\Controller;

abstract class AbstractController
{
    public function __construct(string $action, array $params)
    {
       call_user_func_array([$this, $action], $params);
    }

   
    public function renderJSON(array $content): void    
    {
        header("content-type: application/json");
        echo json_encode($content);
        exit;
    }
}
