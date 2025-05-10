<x-filament-panels::page>
    <div class="flex flex-col items-start justify-start">
        <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white mb-4">Informacje o serwerze</h1>

        <h2 class="text-xl font-semibold tracking-tight text-gray-950 dark:text-white">Aplikacja</h2>
        <ul class="mb-4 w-fit text-gray-950 list-disc list-inside dark:text-gray-400">
            <li class="text-gray-950 dark:text-gray-400">Nazwa aplikacji: {{ config('app.name') }}</li>
            <li class="text-gray-950 dark:text-gray-400">URL aplikacji: {{ config('app.url') }}</li>
            <li class="text-gray-950 dark:text-gray-400">Środowisko: {{ config('app.env') }}</li>
            <li class="text-gray-950 dark:text-gray-400">Debugowanie: {{ config('app.debug') }}</li>
            <li class="text-gray-950 dark:text-gray-400">Język aplikacji: {{ config('app.locale') }}</li>
        </ul>

        <h2 class="text-xl font-semibold tracking-tight text-gray-950 dark:text-white">Biblioteki (npm)</h2>
        <ul class="mb-4 w-fit text-gray-950 list-disc list-inside dark:text-gray-400">
            @foreach ($npmPackages as $package)
                <li class="text-gray-950 dark:text-gray-400" style="color: {{ $package['is_up_to_date'] ? 'green' : 'red' }}">{{ $package['name'] }}{{ $package['current_version'] ? ', Wersja: ' . $package['current_version'] : null }}{{ !$package['is_up_to_date'] ? ' -> Nowsza wersja: ' . $package['latest_version'] : null }}</li>
            @endforeach
        </ul>
        
        <h2 class="text-xl font-semibold tracking-tight text-gray-950 dark:text-white">Biblioteki (composer)</h2>
        <ul class="mb-4 w-fit text-gray-950 list-disc list-inside dark:text-gray-400">
            @foreach ($composerPackages as $package)
                <li class="text-gray-950 dark:text-gray-400" style="color: {{ $package['is_up_to_date'] ? 'green' : 'red' }}">{{ $package['name'] }}{{ $package['current_version'] ? ', Wersja: ' . $package['current_version'] : null }}{{ !$package['is_up_to_date'] ? ' -> Nowsza wersja: ' . $package['latest_version'] : null }}</li>
            @endforeach
        </ul>

        <h2 class="text-xl font-semibold tracking-tight text-gray-950 dark:text-white">PHP {{ phpversion() }}</h2>

        <?php
            $json = file_get_contents('https://www.php.net/releases/?json');
            $data = json_decode($json, true);

            if (is_array($data) && !empty($data)) {
                $versions = array_keys($data);
                usort($versions, 'version_compare');
                $latest = end($versions);

                $latestVersionObj = $data[$latest];
                $latestVersionField = $latestVersionObj['version'] ?? $latest;

                echo "<p class='font-semibold underline text-gray-950 dark:text-gray-400'>Najnowsza wersja PHP: $latestVersionField</p>";
            } else {
                echo "<p class='font-semibold underline text-gray-950 dark:text-gray-400'>Nie udało się pobrać danych o wersjach PHP.</p>";
            }
        ?>

        <p class="text-lg text-gray-950 dark:text-white">Załadowane moduły:</p>
        <ul class="mb-4 w-fit text-gray-950 list-disc list-inside dark:text-gray-400">
            <?php
            $modules = get_loaded_extensions();
            foreach ($modules as $module) {
                echo '<li class="text-gray-950 dark:text-gray-400">' . $module . ', Wersja: ' . phpversion($module) . "</li>";
            }
            ?>
        </ul>
    </div>
</x-filament-panels::page>