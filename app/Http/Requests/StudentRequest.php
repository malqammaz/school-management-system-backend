<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by policies
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [];
        $user = $this->user();
        $student = $this->route('student');

        if ($this->isMethod('POST')) {
            // Creating a new student
            $rules = [
                'user_id' => 'required|exists:users,id|unique:students,user_id',
                'assigned_class_id' => 'nullable|exists:classrooms,id',
                'grade' => 'nullable|numeric|min:0|max:100',
            ];
        } elseif ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // Updating an existing student
            if ($user->role === 'admin') {
                $rules = [
                    'assigned_class_id' => 'nullable|exists:classrooms,id',
                    'grade' => 'nullable|numeric|min:0|max:100',
                    'name' => 'nullable|string|max:255',
                    'email' => [
                        'nullable',
                        'email',
                        'max:255',
                        Rule::unique('users', 'email')->ignore($student->user->id ?? null),
                    ],
                    'date_of_birth' => 'nullable|date|before:today',
                ];
            } elseif ($user->role === 'teacher') {
                $rules = [
                    'assigned_class_id' => 'nullable|exists:classrooms,id',
                    'grade' => 'nullable|numeric|min:0|max:100',
                ];
            } elseif ($user->role === 'student') {
                $rules = [
                    'name' => 'nullable|string|max:255',
                    'email' => [
                        'nullable',
                        'email',
                        'max:255',
                        Rule::unique('users', 'email')->ignore($student->user->id ?? null),
                    ],
                    'date_of_birth' => 'nullable|date|before:today',
                ];
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'A user ID is required to create a student.',
            'user_id.exists' => 'The specified user does not exist.',
            'user_id.unique' => 'This user is already registered as a student.',
            'assigned_class_id.exists' => 'The specified classroom does not exist.',
            'grade.numeric' => 'Grade must be a number.',
            'grade.min' => 'Grade cannot be less than 0.',
            'grade.max' => 'Grade cannot be greater than 100.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'date_of_birth.date' => 'Please provide a valid date.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
        ];
    }
}
