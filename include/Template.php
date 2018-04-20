<?php
/**
 *
 */

namespace PureFTPAdmin;


use PureUserFTP\Form\Form;

class Template
{

    private $variables = [];

    /**
     * @param string $page_title
     */
    public function __construct($page_title = '')
    {
        $this->variables['page_title'] = $page_title;

    }

    /**
     * @param string $variable
     * @param mixed $value
     * @return void
     */
    public function assign($variable, $value)
    {
        if($value instanceof \PureFTPAdmin\Form\Form) {
            $value = $value->render();
        }

        $this->variables[$variable] = $value;
    }

    /**
     * @param string $body_template - inner template.
     * @return string html hopefully.
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function display($body_template)
    {

        $loader = new \Twig_Loader_Filesystem(dirname(__FILE__) . '/../templates');

        $twig = new \Twig_Environment($loader);

        $variables = $this->variables;

        $body = $twig->load($body_template);
        
        $variables['body_template'] = $body;

        return $twig->render('master.twig', $variables);
    }
}
