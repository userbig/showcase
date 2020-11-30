<?php


namespace App\Repositories\EditorJS;


use App\Exceptions\CustomException;
use App\Repositories\BodyBuilder\BodyBuilder;
use App\Repositories\BodyBuilder\Tools\ProductBody\ColumnBlock;
use App\Repositories\BodyBuilder\Tools\ProductBody\TitleBlock;
use Illuminate\Http\Request;

class ProductRequestSanitizer
{

    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Repositories\BodyBuilder\Exceptions\BodyBuilderException
     * @throws CustomException
     */
    public static function validateEditor(Request $request)
    {
        $content['id'] = $request->get('id');
        $content['data'] = $request->validated();
        unset($content['data']['id']);

        // inputs which will be validated separately
        $accepted = [
            'id', 'specs', 'slug', 'is_published', 'is_popular'
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
            && empty($content['data']['specs']['blocks'])) {
            throw new CustomException('Content cannot be empty', 400);
        }

        // validate and sanitize body blocks
        $content['data']['specs'] = (new BodyBuilder($content['data']['specs'],
            [
                ColumnBlock::class,
                TitleBlock::class
            ]
        ))->sanitize()->getBlocks();

        return $content;

    }
}
