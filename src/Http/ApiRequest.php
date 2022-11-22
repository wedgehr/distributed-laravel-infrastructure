<?php

namespace DistributedLaravel\Infrastructure\Http;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use DistributedLaravel\Infrastructure\Exceptions\ValidationException;

/**
 * ApiRequest
 *
 * @method array validated(string|null $key = null, mixed $default = null)
 */
abstract class ApiRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function logFailedPayload(): bool
	{
		return false;
	}

	protected function failedValidation(Validator $validator): void
	{
		$data = $this->all();

		$ldata = [
			'errors' => $validator->errors()->toArray(),
			'JSON' => $validator->errors()->toJson(),
		];

		if ($this->logFailedPayload()) {
			$ldata['payload'] = $data;
		}

		Log::info('Validation failed', $ldata);

		throw new ValidationException($validator->errors()->toJson());
	}

	protected function failedAuthorization(): void
	{
		throw new HttpException(403);
	}

	/**
	 * @return array<string, string>
	 */
	public function rules(): array
	{
		return [ ];
	}

	/**
	 * @return array<string, string>
	 */
	public function attributes(): array
	{
		return [ ];
	}
}
