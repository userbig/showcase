<?php


namespace App\Repositories\EditorJS\Tools;


use App\Repositories\EditorJS\Interfaces\ToolInterface;

abstract class AbstractTool implements ToolInterface
{
    abstract public function getSignature();

    abstract public function getTag();

    abstract public function handle($data);
}