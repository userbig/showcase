<?php


namespace App\Repositories\EditorJS\Tools;


class Delimiter extends AbstractTool
{
    /**
     * @return string
     */
    public function getSignature()
    {
        return 'delimiter';
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return '<div class="block-delimiter"></div>';
    }

    /**
     * @param $data
     * @return string
     */
    public function handle($data)
    {
        return $this->wrap();
    }

    /**
     * @return string
     */
    private function wrap()
    {
        return '<figure>' . $this->getTag() . '</figure>';
    }
}