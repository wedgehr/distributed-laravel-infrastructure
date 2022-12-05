<?php

namespace DistributedLaravel\Infrastructure\Services;

use LogicException;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use DistributedLaravel\Infrastructure\Exceptions\Throwables\NotFoundException;
use DistributedLaravel\Infrastructure\Exceptions\Throwables\ForbiddenException;
use DistributedLaravel\Infrastructure\Database\Eloquent\Model as InfrastructureModel;

abstract class Service extends ServiceProvider
{
	protected AuthManager $auth;
	protected Dispatcher $events;

	protected bool $authorization = true;
	protected bool $muteEvents = false;
	protected ?string $serviceModel = null;

	public function __construct()
	{
		$this->initBaseService();
	}

	// used when the child service has its own constructor that overrides BaseService::construct();
	public function initBaseService(): void
	{
		$this->auth = App::make(AuthManager::class);
		$this->events = App::make(Dispatcher::class);

		$this->serviceModel = '\\' . preg_replace(
			['/Service$/', '/\\\Services\\\/'],
			['', '\\Models\\'],
			static::class
		);
	}

	public function getAccount(bool $required = false)
	{
		$account = $this->auth->user();

		if ($account) {
			return $account;
		}


		if ($required) {
			Log::warning('unable to locate account');
			ForbiddenException::throw();
		}

		Log::info('no authenticated user');

		return null;
	}

	public function authorize(string $ability, ...$contexts): void
	{
		if (! $this->shouldAuthorize()) {
			// authorization disabled
			return;
		}

		$account = $this->getAccount();

		if (! $account) {
			Log::info('Rejecting authorization', [
				'ability' => $ability,
				'reason' => 'no authenticated user',
				'serviceModel' => $this->getServiceModel(),
			]);

			ForbiddenException::throw();
		}

		if (empty($contexts)) {
			$contexts = [
				$this->getServiceModel(),
			];
		}

		foreach ($contexts as $context) {
			$can = false;
			[$type, $id] = $this->detectResourceType($context);

			if ($context instanceof Collection) {
				$type .= '.Collection';
				if (! $context->count()) {
					// we have an empty collection, return
					continue;
				}
				$context->each(function ($x) use (&$can, $account, $ability) {
					return $can = $account->can($ability, $x);
				});
			} else {
				$can = $account->can($ability, $context);
			}

			if (! $can) {
				Log::info('Rejecting authorization', ['ability' => $ability, 'type' => $type, 'id' => $id]);
				ForbiddenException::throw();
			}
		}

		if (config('app.debug')) {
			Log::debug('Accepted authorization', ['ability' => $ability, 'type' => $type, 'id' => $id]);
		}
	}

	public function detectResourceType($resource): array
	{
		if (is_string($resource)) {
			return [$resource, null];
		}

		if ($resource instanceof Collection) {
			$resource = $resource->first();
		}

		// try to get a resource name and id if we can
		if (empty($resource) || ! is_object($resource)) {
			return [null, null];
		}

		$t = explode('\\', get_class($resource));
		$type = array_pop($t);
		$id = $resource->id;

		return [$type, $id];
	}

	public function detectChange(string $key, Model $model, array $data): bool
	{
		if (! isset($data[$key])) {
			return false;
		}

		return $model->$key !== $data[$key];
	}

	public function detectAnyChange(array $keys, Model $model, array $data): bool
	{
		return collect($keys)->filter(fn ($k) => $this->detectChange($k, $model, $data))->count() > 0;
	}

	public function shouldAuthorize(): bool
	{
		return $this->authorization;
	}

	public function withoutAuthorization(): self
	{
		$that = clone $this;

		return $that->setAuthorization(false);
	}

	public function withAuthorization(): self
	{
		$that = clone $this;

		return $that->setAuthorization(true);
	}

	public function setAuthorization(bool $authorize): self
	{
		$this->authorization = $authorize;

		return $this;
	}

	public function instanceOfServiceModel(int|InfrastructureModel $m): bool
	{
		if (is_int($m)) {
			return false;
		}

		$sm = $this->getServiceModel();

		if (! $sm) {
			throw new \LogicException('service model not defined');
		}

		return $m instanceof $sm;
	}

	public function getId(int|InfrastructureModel $m): int
	{
		return getId($m);
	}

	public function get(int|InfrastructureModel $m)
	{
		if ($this->instanceOfServiceModel($m)) {
			return $m;
		}

		return $this->getById($m);
	}

	public function getById(int $id)
	{
		$sm = $this->getServiceModel();

		if (! $sm) {
			throw new LogicException('unable to locate service model');
		}

		$result = $sm::find($id);

		if (empty($result)) {
			Log::error('resource not found', ['model' => $sm, 'id' => $id]);

			throw new NotFoundException();
		}

		$this->authorize('view', $result);

		return $result;
	}

	public function deriveId($id): ?int
	{
		if (is_int($id) || is_numeric($id)) {
			return (int) $id;
		}

		if ($this->instanceOfServiceModel($id)) {
			return $id->id;
		}

		return null;
	}

	public function mute(false|array $x = []): self
	{
		if ($x === false) {
			$this->muteEvents = false;

			return $this;
		}

		$this->muteEvents = empty($x) ? true : $x;

		return $this;
	}

	public function unmute(): self
	{
		$this->muteEvents = false;

		return $this;
	}

	public function getServiceModel(): ?string
	{
		return $this->serviceModel;
	}
}
