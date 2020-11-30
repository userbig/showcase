<?php


namespace App\Repositories\EditorJS\Tools;


class Warning extends AbstractTool
{

    public function getSignature()
    {
        return 'warning';
    }

    public function getTag()
    {
        // TODO: Implement getTag() method.
    }

    public function handle($data)
    {
        return $this->wrap($data['title'], $data['message']);
    }

    public function wrap(string $title, string $message)
    {
        return
            '<figure class="warn">' .
                '<div class="warn__title">' .
                     $title .
                '</div>' .
                '<div class="warn_message">' .
                    $message .
                '</div>' .
            '</figure>';
    }
}
