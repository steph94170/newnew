<?php

namespace App\Class;

use Mailjet\Client;
use Mailjet\Resources;


class Mail 
{
    public function send($to_email, $to_name, $subject, $template, $vars = null)
    {
        //Récuperation du template
        $content = file_get_contents(dirname(__DIR__).'/Mail/'.$template);

        //récuperer les variable  facultatives
        if ($vars) {
            foreach($vars as $key=>$var) {
                $content = str_replace('{'.$key.'}', $var, $content);
            }
        }

     

        $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'],$_ENV['MJ_APIKEY_PRIVATE'],true,['version' => 'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "amanikoffi77330@gmail.com",
                        'Name' => "Chaussure-Chic"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 6133117,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                            'content' => $content
                    ],
                ]
            ]
        ];

        // All resources are located in the Resources class

        $mj->post(Resources::$Email, ['body' => $body]);


    }
}

