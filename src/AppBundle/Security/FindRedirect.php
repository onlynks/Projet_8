<?php

namespace AppBundle\Security;

abstract class FindRedirect
{

    protected $regex = [
        'delete' => ['#(^/?tasks/[0-9][0-9]?[0_9]?/delete$)+#', 'task_list'],
        'update' => '#(^/?tasks/[0-9][0-9]?[0_9]?/update)+#',
        ]
    ;

    protected function getRedirectPath($pathInfo) {

        foreach ( $this->regex as $key => $value) {

            if(preg_match($value[0], $pathInfo)) {
                return $value[1];
            }
        }

    }
}