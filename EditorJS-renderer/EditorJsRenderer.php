<?php

namespace App\Repositories\EditorJS;

use App\Repositories\EditorJS\Exceptions\SignatureAlreadyAssignedException;
use App\Repositories\EditorJS\Exceptions\ToolNotFoundException;
use App\Repositories\EditorJS\Exceptions\WrongInterfaceException;
use App\Repositories\EditorJS\Tools\AbstractTool;
use App\Repositories\EditorJS\Tools\Delimiter;
use App\Repositories\EditorJS\Tools\Header;
use App\Repositories\EditorJS\Tools\Image;
use App\Repositories\EditorJS\Tools\Lists;
use App\Repositories\EditorJS\Tools\Paragraph;
use App\Repositories\EditorJS\Tools\Quote;
use App\Repositories\EditorJS\Tools\Warning;

class EditorJsRenderer
{
    /**
     * List of tools
     *
     * @var array
     */
    protected array $tools = [];

    /**
     * EditorJsRenderer constructor.
     * @throws SignatureAlreadyAssignedException
     * @throws ToolNotFoundException
     * @throws WrongInterfaceException
     */
    public function __construct()
    {
        $this->addTools([
            Header::class,
            Paragraph::class,
            Quote::class,
            Lists::class,
            Image::class,
            Delimiter::class,
            Warning::class,
        ]);
    }


    /**
     * @param array $blocks
     * @return string
     */
    public static function render(array $blocks)
    {
        $renderer = new static();

        $html = [];

        foreach ($blocks as $block) {
            $html[] = $renderer->handleBlock($block['type'], $block['data']);

            unset($block);
        }

        return implode('', $html);
    }

    /**
     * @param string $signature
     * @param $data
     * @return false
     */
    public function handleBlock(string $signature, $data)
    {
        $tool = $this->tools[$signature] ?? null;

        return $tool ? $tool->handle($data) : false;
    }

    /**
     * @param array $tools
     * @throws ToolNotFoundException|WrongInterfaceException|SignatureAlreadyAssignedException
     */
    public function addTools(array $tools)
    {
        foreach ($tools as $tool) {
            $this->addTool($tool);
        }
    }

    /**
     * @param $tool
     * @throws ToolNotFoundException
     * @throws WrongInterfaceException
     * @throws SignatureAlreadyAssignedException
     */
    public function addTool($tool): void
    {
        $tool = $this->resolveTool($tool);

        if (array_key_exists($tool->getSignature(), $this->tools)) {
            throw new SignatureAlreadyAssignedException(
                sprintf(
                    'Signature "%s" already assigned in list of tools',
                    $tool->getSignature()
                ),
            );
        } else {
            $this->tools[$tool->getSignature()] = $tool;
        }
    }

    /**
     * @param $tool
     * @return AbstractTool
     * @throws ToolNotFoundException
     * @throws WrongInterfaceException
     */
    public function resolveTool($tool): AbstractTool
    {
        $tool = $this->makeToolObj($tool);

        if (!($tool instanceof AbstractTool)) {
            throw new WrongInterfaceException(
                sprintf(
                    'Tool "%s" must be an instance of App\Repositories\EditorJS\Interfaces\ToolInterface',
                    get_class($tool)
                )
            );
        }

        return $tool;
    }

    /**
     * Return tool object
     *
     * @param $tool
     * @return mixed
     * @throws ToolNotFoundException
     */
    public function makeToolObj($tool)
    {
        if (is_object($tool)) {
            return $tool;
        }

        if (!class_exists($tool)) {
            throw new ToolNotFoundException(sprintf('Tool block "%s" not found', $tool));
        }

        return new $tool;
    }
}
