<?php


namespace App\Repositories\EditorJS\Tools;


class Specification extends AbstractTool
{

    public function getSignature()
    {
        return 'specification';
    }

    public function getTag()
    {
        // TODO: Implement getTag() method.
    }

    public function handle($data)
    {
        return
            "<div>" .
                "<div class='caption'>" .
                    $data['caption'] .
                "</div>" .

                "<div class='text'>".
                    $data['text'] .
                "</div>".
            "</div>";
    }
}
