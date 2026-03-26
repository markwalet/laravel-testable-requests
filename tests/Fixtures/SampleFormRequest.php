<?php

namespace MarkWalet\TestableRequests\Tests\Fixtures;

use Illuminate\Foundation\Http\FormRequest;

class SampleFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can_submit;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'min:3'],
            'email' => ['required', 'email'],
            'account' => ['required', 'in:'.$this->route('account')],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'A title is required.',
        ];
    }
}
