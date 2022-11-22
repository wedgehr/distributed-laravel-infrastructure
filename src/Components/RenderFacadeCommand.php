<?php

namespace DistributedLaravel\Infrastructure\Components;

use Symfony\Component\Finder\Finder;
use Cumulati\LaravelFacadeGenerator\FacadeManager;
use DistributedLaravel\Infrastructure\Console\Command;

class RenderFacadeCommand extends Command
{
	protected $signature = 'components:render-facades';
	protected $description = 'render facades';

	public function handle()
	{
		$namespaces = config('optimus.components.namespaces');

		foreach ($namespaces as $ns => $component) {
			if (! is_array($component)) {
				continue;
			}

			$path = $component['path'];

			$finder = new Finder();
			$dirs = $finder->depth('< 1')->directories()->in($path);

			foreach ($dirs as $d) {
				$entityPath = $d->getRealPath();
				$parts = explode('/', $entityPath);
				$entity = array_pop($parts);

				$servicesPath = $entityPath . '/Services';
				if (! file_exists($servicesPath)) {
					continue;
				}

				$sFinder = new Finder();
				$services = $sFinder->depth('< 1')->files()->in($servicesPath);

				foreach ($services as $service) {
					$serviceName = $service->getFilenameWithoutExtension();

					$rootClass = sprintf('%s\\%s\\Services\\%s', $ns, $entity, $serviceName);
					$facadeClass = sprintf('%s\\%s\\Facades\\%s', $ns, $entity, $serviceName);
					$facadePath = sprintf('%s/Facades/%s.php', $entityPath, $serviceName);

					(new FacadeManager($facadeClass, $facadePath))
						->renderFacade($rootClass);

					$this->linef('Generated facade for %s', $serviceName);
				}
			}
		}
	}
}
