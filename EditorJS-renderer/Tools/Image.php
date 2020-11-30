<?php


namespace App\Repositories\EditorJS\Tools;


class Image extends AbstractTool
{
    /**
     * @return string
     */
    public function getSignature()
    {
        return 'image';
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return 'img';
    }

    /**
     * @param $data
     * @return string
     */
    public function handle($data)
    {
        return $this->wrap(
            $data['url'],
            $data['caption'],
            $data['withBorder'],
            $data['withBackground'],
            $data['stretched']
        );
    }

    /**
     * @param string $url
     * @param string $caption
     * @param bool $withBorder
     * @param bool $withBackground
     * @param bool $stretched
     * @return string
     */
    private function wrap(string $url, string $caption, bool $withBorder, bool $withBackground, bool $stretched)
    {
        $html =
            '<figure>' .
                '<img src="' . $url . '">' .
                '<figcaption>'. $caption .'</figcaption>' .
            '</figure>';

        return $html;

    }
}