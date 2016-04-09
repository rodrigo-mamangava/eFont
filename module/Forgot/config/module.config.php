<?php
return array(
    'factories' => array(
        'Forgot\Model\ForgotTable' => function ($sm)
        {
            $dbAdapter = $sm->get('Adapter');
            return new Forgot\Model\ForgotTable($dbAdapter);
        }
    )
);