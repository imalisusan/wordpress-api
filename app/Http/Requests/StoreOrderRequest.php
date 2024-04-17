<?php

namespace App\Http\Requests;

use App\Helpers\CraydelHelperFunctions;
use App\Helpers\CraydelJSONResponseHelper;
use App\Http\Controllers\Commands\ManageStoreEntryController;
use App\Traits\CanLog;
use App\Traits\CanRespond;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreOrderRequest extends FormRequest
{
    use CanLog;
    use CanRespond;

    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string
     */
    public function rules(): array
    {
        return ManageStoreEntryController::validateOrderData();
    }

    /**
     * Handle a failed validation attempt.
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        $response = $this->respondInJSON(new CraydelJSONResponseHelper(
            false,
            call_user_func(function () use ($validator) {
                $messages = $validator->messages()->messages();

                if (is_string(current($messages)[0])) {
                    return current($messages)[0] ?? 'Error while validating the career pathway details';
                }

                if (is_array(current($messages)[0])) {
                    $currentMessages = current($messages)[0] ?? [];
                    $keys = array_keys($currentMessages);

                    return $currentMessages[$keys[0]] ?? 'Error while validating the career pathway details';
                }

                return 'Error while validating the career pathway details';
            })
        ));

        throw (new ValidationException($validator, $response))
            ->errorBag($this->errorBag);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Prepare the data for validation.
     *
     * @throws Exception
     */
    protected function prepareForValidation(): void
    {
        $empty_fields = collect($this->all())->filter(function ($value) {
            return $value === null || CraydelHelperFunctions::isNull($value) || $value === '""';
        });

        foreach ($empty_fields as $key => $field) {
            if ($field !== null) {
                $this->request->remove($key);
            }
        }
    }
}
