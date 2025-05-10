<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Card;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Composer\InstalledVersions;
use Symfony\Component\Process\Process;

class Diagnostic extends Page
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('view_diagnostics') || auth()->user()->can('admin');
    }
    
    protected static ?string $navigationIcon = 'heroicon-o-information-circle';
    protected static ?string $navigationGroup = 'Ustawienia';
    protected static ?string $title = 'Diagnostyka';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.diagnostic-page';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FourExtraLarge;
    }

    public array $composerPackages = [];
    public array $npmPackages = [];

    public function mount(): void
    {
        $composerJson = json_decode(file_get_contents(base_path('composer.json')), true);

        $direct = [];
        foreach (['require', 'require-dev'] as $section) {
            if (isset($composerJson[$section])) {
                foreach ($composerJson[$section] as $name => $ver) {
                    if (strpos($name, 'ext-') !== 0 && $name !== 'php') {
                        $direct[] = $name;
                    }
                }
            }
        }

        foreach ($direct as $package) {
            if (\Composer\InstalledVersions::isInstalled($package)) {
                $this->composerPackages[$package] = [
                    'name' => $package,
                    'current_version' => \Composer\InstalledVersions::getPrettyVersion($package),
                    'latest_version' => null,
                    'is_up_to_date' => true,
                ];
            }
        }

        try {
            $env = $_ENV;
            $env['HOME'] = base_path();
            $composerProcess = new Process(['composer', 'outdated', '--format=json', '--direct'], base_path(), $env);
            $composerProcess->run();

            if ($composerProcess->isSuccessful()) {
                $outdatedComposerPackages = json_decode($composerProcess->getOutput(), true);

                foreach ($outdatedComposerPackages['installed'] ?? [] as $outdatedPackage) {
                    if (isset($this->composerPackages[$outdatedPackage['name']])) {
                        $this->composerPackages[$outdatedPackage['name']]['latest_version'] = $outdatedPackage['latest'];
                        $this->composerPackages[$outdatedPackage['name']]['is_up_to_date'] = false;
                    }
                }
            } else {
                throw new \Exception($composerProcess->getErrorOutput());
            }
        } catch (\Exception $e) {
            dd('Composer Error: ' . $e->getMessage());
        }

        try {
            $packageJsonPath = base_path('package.json');
            if (file_exists($packageJsonPath)) {
                $packageJsonContent = json_decode(file_get_contents($packageJsonPath), true);

                $outdatedNpmPackages = [];

                $env = $_ENV;
                $env['HOME'] = base_path();
                $npmProcess = new Process(['npm', 'outdated', '--json'], base_path(), $env);
                $npmProcess->run();

                $outdatedNpmPackages = json_decode($npmProcess->getOutput(), true);

                foreach (['dependencies', 'devDependencies'] as $dependencyType) {
                    if (isset($packageJsonContent[$dependencyType])) {
                        foreach ($packageJsonContent[$dependencyType] as $name => $version) {
                            $this->npmPackages[] = [
                                'name' => $name,
                                'current_version' => isset($outdatedNpmPackages[$name]) ? $outdatedNpmPackages[$name]['current'] : $version,
                                'wanted_version' => isset($outdatedNpmPackages[$name]) ? $outdatedNpmPackages[$name]['wanted'] : null,
                                'latest_version' => isset($outdatedNpmPackages[$name]) ? $outdatedNpmPackages[$name]['latest'] : null,
                                'is_up_to_date' => !isset($outdatedNpmPackages[$name]),
                            ];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            dd('NPM Error: ' . $e->getMessage());
        }
    }
}