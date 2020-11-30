<?php


namespace App\Repositories\EditorJS\Tools;


class Quote extends AbstractTool
{
    /**
     * @return string
     */
    public function getSignature()
    {
        return 'quote';
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return 'blockquote';
    }

    /**
     * @param $data
     * @return string
     */
    public function handle($data)
    {
        return $this->wrap($data['text'], $data['caption'], $data['alignment']);
    }

    /**
     * @param string $text
     * @param string $caption
     * @param string $alignment
     * @return string
     */
    private function wrap(string $text, string $caption, string $alignment)
    {
        return
            '<figure class="">' .
                '<blockquote>' .
                    $text .
                '</blockquote>' .
                '<figcaption>'. $caption .'</figcaption>' .
            '</figure>';
    }
}