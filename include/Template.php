<?php
/**
 *
 */

namespace PureFTPAdmin;


use PureUserFTP\Form\Form;

class Template
{

    /**
     * @var array
     */
    private $variables = [];

    /**
     * @param string $page_title
     */
    public function __construct(string $page_title = '')
    {
        $this->variables['page_title'] = $page_title;

    }

    /**
     * @param string $variable
     * @param mixed $value
     * @return void
     */
    public function assign(string $variable, $value): void
    {
        if ($value instanceof \PureFTPAdmin\Form\Form) {
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
    public function display(string $body_template): string
    {

        $loader = new \Twig\Loader\FilesystemLoader(dirname(__FILE__) . '/../templates');

        $twig = new \Twig\Environment($loader);


        $variables = $this->variables;

        $variables['body_template'] = $body_template;

        return $twig->render('master.twig', $variables);
    }
}
