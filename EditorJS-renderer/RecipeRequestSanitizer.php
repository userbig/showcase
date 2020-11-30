<?php


namespace App\Repositories\EditorJS;


use App\Exceptions\CustomException;
use EditorJS\EditorJS;
use Illuminate\Http\Request;

class RecipeRequestSanitizer
{
    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * @throws \EditorJS\EditorJSException
     */
    public static function validateEditor(Request $request)
    {
        $content['id'] = $request->get('id');
        $content['data'] = $request->validated();
        unset($content['data']['id']);

        // inputs which will be validated separately
        $accepted = [
            'id', 'body', 'ingredients', 'slug', 'is_published'
        ];

        // getting array key => rules of sanitized inputs
        $rule_keys = $request->rules();
        $accepted_keys = array_flip($accepted);
        $sanitized = array_diff_key($rule_keys, $accepted_keys);
        foreach ($sanitized as $key => &$rule) {
            $rule = $request->get($key);
        }

        // checking if sanitized inputs are not fully null
        $isSanitizedContainsOnlyNull = (empty(array_filter($sanitized, function ($a) {
            return $a !== null;
        })));


        // checking if sanitized and editor body blocks is not empty
        if ($isSanitizedContainsOnlyNull
            && empty($content['data']['body']['blocks'])
            && empty($content['data']['body']['ingredients'])) {
            throw new CustomException('Content cannot be empty', 400);
        }

        // validate and sanitize body blocks
        $editorBody = new EditorJS(json_encode($content['data']['body']), json_encode(self::getConfig()));

        // validate and sanitize ingredients blocks
        $editorIngredients = new EditorJS(json_encode($content['data']['ingredients']), json_encode(self::getConfig()));


        $content['data']['body'] = $editorBody->getBlocks();
        $content['data']['ingredients'] = $editorIngredients->getBlocks();

        return $content;
    }

    /**
     * Config for validator
     *
     * @return array[]
     */
    protected static function getConfig()
    {
        return [
            'tools' => [
                'header'    => [
                    'text'  => [
                        'type'        => 'string',
                        'allowedTags' => ''
                    ],
                    'level' => [
                        'type'      => 'int',
                        'canBeOnly' => [2, 3, 4]
                    ]
                ],
                'paragraph' => [
                    'text' => [
                        'type'        => 'string',
                        'allowedTags' => 'i,b,u,a[href],code[class]'
                    ]
                ],
                'quote'     => [
                    'text'      => [
                        'type'        => 'string',
                        'allowedTags' => 'i,b,u'
                    ],
                    'caption'   => [
                        'type' => 'string'
                    ],
                    'alignment' => [
                        'type'      => 'string',
                        'canBeOnly' => ['left', 'center']
                    ]
                ],
                'list'      => [
                    'items' => [
                        'type' => 'array',
                        'data' => [
                            '-' => [
                                'type'        => 'string',
                                'allowedTags' => 'i,b,u'
                            ],
                        ],
                    ],
                    'style' => [
                        'type'      => 'string',
                        'canBeOnly' => ['ordered', 'unordered']
                    ]
                ],
                'image'     => [
                    'caption'        => [
                        'type'        => 'string',
                        'required'    => false,
                        'allow_null'  => true,
                        'allowedTags' => 'i, b, a[href], code[class], mark[class]',
                    ],
                    'stretched'      => [
                        'type' => 'boolean'
                    ],
                    'url'            => [
                        'type' => 'string',
                    ],
                    'withBackground' => [
                        'type' => 'boolean'
                    ],
                    'withBorder'     => [
                        'type' => 'boolean'
                    ]
                ],
                'delimiter' => [
                ],
                'warning'   => [
                    'title'   => [
                        'type' => 'string'
                    ],
                    'message' => [
                        'type'       => 'string',
                        'required'   => false,
                        'allow_null' => true,
                    ]
                ]
            ]
        ];
    }
}
