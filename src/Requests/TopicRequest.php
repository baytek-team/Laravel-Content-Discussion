<?php

namespace Baytek\Laravel\Content\Types\Discussion\Requests;

use App\Http\Requests\Request;
use Baytek\Laravel\Content\Models\Content;

class TopicRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|unique_in_type:discussion-topic',
        ];
    }
}
