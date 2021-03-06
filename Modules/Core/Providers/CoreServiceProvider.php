<?php

namespace Modules\Core\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Console\InstallCommand;
use Modules\Core\Console\PublishModuleAssetsCommand;
use Modules\Core\Console\PublishThemeAssetsCommand;
use Modules\Core\Foundation\Theme\ThemeManager;
use Modules\Core\Traits\CanPublishConfiguration;
use Nwidart\Modules\Module;

class CoreServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * @var string
     */
    protected $prefix = 'asgard';

    /**
     * The filters base class name.
     *
     * @var array
     */
    protected $middleware = [
        'Core' => [
            'permissions'           => 'PermissionMiddleware',
            'auth.admin'            => 'AdminMiddleware',
            'public.checkLocale'    => 'PublicMiddleware',
            'localizationRedirect'  => 'LocalizationMiddleware',
            'can' => 'Authorization',
        ],
    ];

    public function boot()
    {
        $this->registerMiddleware($this->app['router']);
        $this->registerModuleResourceNamespaces();

        $this->publishConfig('core', 'available-locales');
        $this->publishConfig('core', 'config');
        $this->publishConfig('core', 'core');
        $this->publishConfig('core', 'settings');

        $this->bladeDirectives();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('asgard.isInstalled', function () {
            return true === env('INSTALLED', false);
        });

        $this->registerCommands();
        $this->registerServices();
        $this->setLocalesConfigurations();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    /**
     * Register the filters.
     *
     * @param  Router $router
     * @return void
     */
    public function registerMiddleware(Router $router)
    {
        foreach ($this->middleware as $module => $middlewares) {
            foreach ($middlewares as $name => $middleware) {
                $class = "Modules\\{$module}\\Http\\Middleware\\{$middleware}";

                $router->middleware($name, $class);
            }
        }
    }

    /**
     * Register the console commands
     */
    private function registerCommands()
    {
        $this->commands([
            InstallCommand::class,
            PublishThemeAssetsCommand::class,
            PublishModuleAssetsCommand::class,
        ]);
    }

    private function registerServices()
    {
        $this->app->singleton(ThemeManager::class, function ($app) {
            $path = $app['config']->get('asgard.core.core.themes_path');

            return new ThemeManager($app, $path);
        });
    }

    /**
     * Register the modules aliases
     */
    private function registerModuleResourceNamespaces()
    {
        foreach ($this->app['modules']->getOrdered() as $module) {
            $this->registerViewNamespace($module);
            $this->registerLanguageNamespace($module);
        }
    }

    /**
     * Register the view namespaces for the modules
     * @param Module $module
     */
    protected function registerViewNamespace(Module $module)
    {
        if ($module->getLowerName() == 'user') {
            return;
        }
        $this->app['view']->addNamespace(
            $module->getLowerName(),
            $module->getPath() . '/Resources/views'
        );
    }

    /**
     * Register the language namespaces for the modules
     * @param Module $module
     */
    protected function registerLanguageNamespace(Module $module)
    {
        $moduleName = $module->getLowerName();

        $langPath = base_path("resources/lang/$moduleName");
        $secondPath = base_path("resources/lang/translation/$moduleName");

        if ($moduleName !== 'translation' && $this->hasPublishedTranslations($langPath)) {
            return $this->loadTranslationsFrom($langPath, $moduleName);
        }
        if ($this->hasPublishedTranslations($secondPath)) {
            return $this->loadTranslationsFrom($secondPath, $moduleName);
        }
        if ($this->moduleHasCentralisedTranslations($module)) {
            return $this->loadTranslationsFrom($this->getCentralisedTranslationPath($module), $moduleName);
        }

        return $this->loadTranslationsFrom($module->getPath() . '/Resources/lang', $moduleName);
    }

    /**
     * @param $file
     * @param $package
     * @return string
     */
    private function getConfigFilename($file)
    {
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', basename($file));
    }

    /**
     * Set the locale configuration for
     * - laravel localization
     * - laravel translatable
     */
    private function setLocalesConfigurations()
    {
        if (! $this->app['asgard.isInstalled']) {
            return;
        }

        $localeConfig = $this->app['cache']
            ->tags('setting.settings', 'global')
            ->remember("asgard.locales", 120,
                function () {
                    return DB::table('setting__settings')->whereName('core::locales')->first();
                }
            );
        if ($localeConfig) {
            $locales = json_decode($localeConfig->plainValue);
            $availableLocales = [];
            foreach ($locales as $locale) {
                $availableLocales = array_merge($availableLocales, [$locale => config("available-locales.$locale")]);
            }

            $laravelDefaultLocale = $this->app->config->get('app.locale');

            if (! in_array($laravelDefaultLocale, array_keys($availableLocales))) {
                $this->app->config->set('app.locale', array_keys($availableLocales)[0]);
            }
            $this->app->config->set('laravellocalization.supportedLocales', $availableLocales);
            $this->app->config->set('translatable.locales', $locales);
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    private function hasPublishedTranslations($path)
    {
        return is_dir($path);
    }

    /**
     * Does a Module have it's Translations centralised in the Translation module?
     * @param Module $module
     * @return bool
     */
    private function moduleHasCentralisedTranslations(Module $module)
    {
        if (! array_has($this->app['modules']->enabled(), 'Translation')) {
            return false;
        }

        return is_dir($this->getCentralisedTranslationPath($module));
    }

    /**
     * Get the absolute path to the Centralised Translations for a Module (via the Translations module)
     * @param Module $module
     * @return string
     */
    private function getCentralisedTranslationPath(Module $module)
    {
        return $this->app['modules']->find('Translation')->getPath() . "/Resources/lang/{$module->getLowerName()}";
    }

    /**
     * List of Custom Blade Directives
     */
    public function bladeDirectives()
    {
        /**
         * Set variable.
         * Usage: @set($variable, value)
         */
        Blade::directive('set', function ($expression) {
            list($variable, $value) = $this->getArguments($expression);

            return "<?php {$variable} = {$value}; ?>";
        });
    }

    /**
     * Get argument array from argument string.
     * @param $argumentString
     * @return array
     */
    private function getArguments($argumentString)
    {
        return str_getcsv($argumentString, ',', "'");
    }
}
