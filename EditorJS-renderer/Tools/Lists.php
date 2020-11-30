<?php


namespace App\Repositories\EditorJS\Tools;


class Lists extends AbstractTool
{


    /**
     * @return string
     */
    public function getSignature()
    {
        return 'list';
    }

    /**
     * @return string|string[]
     */
    public function getTag()
    {
        return [
            'unordered' => 'ul',
            'ordered' => 'ol'
        ];
    }

    /**
     * @param $data
     * @return string
     */
    public function handle($data)
    {
        return $this->wrap($data['items'], $data['style']);
    }


    /**
     * @param array $items
     * @param string $style
     * @return string
     */
    private function wrap(array $items, string $style)
    {
        $html = '<' . $this->getTag()[$style] . '>';

        foreach ($items as $item) {
            $html .= '<li>' . $item . '</li>';

            unset($item);
        }
        $html .= '</' . $this->getTag()[$style] . '>';

        return $html;
    }
}