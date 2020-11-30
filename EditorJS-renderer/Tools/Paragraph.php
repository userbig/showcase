<?php


namespace App\Repositories\EditorJS\Tools;


class Paragraph extends AbstractTool
{
    /**
     * @return string
     */
    public function getSignature()
    {
        return 'paragraph';
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return 'p';
    }

    public function handle($data)
    {
        return $this->wrap($data['text']);
    }

    /**
     * Wrap text in HTML tag
     *
     * @param string $text
     * @return string
     */
    private function wrap(string $text): string
    {
        return '<' . $this->getTag() . '>' . $text . '</' . $this->getTag() . '>';
    }
}