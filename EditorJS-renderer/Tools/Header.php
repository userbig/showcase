<?php


namespace App\Repositories\EditorJS\Tools;


class Header extends AbstractTool
{
    /**
     * @return string
     */
    public function getSignature()
    {
        return 'header';
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return 'h';
    }

    public function handle($data)
    {
        return $this->wrap($data['text'], $data['level']);
    }

    /**
     * Wrap text in HTML tag with level
     *
     * @param string $text
     * @param int $level
     * @return string
     */
    public function wrap(string $text, int $level)
    {
        return '<' . $this->getTag() . $level . '>' . $text . '</' . $this->getTag() . $level . '>';
    }

}