<?php

namespace MarkWalet\TestableRequests\Tests\Fixtures;

use Illuminate\Foundation\Http\FormRequest;

class SampleFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) data_get($this->user(), 'can_submit');
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'min:3'],
            'email' => ['required', 'email'],
            'account' => ['required', 'in:'.$this->route('account')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'A title is required.',
        ];
    }
}
