<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\StoreBorrowingRequest as BaseRequest;
use Illuminate\Validation\Rule;

class StoreBorrowingRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'peminjam')],
        ]);
    }

    public function attributes(): array
    {
        return array_merge(parent::attributes(), [
            'user_id' => 'peminjam',
        ]);
    }
}