<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel SPA') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18]">
        <div id="app" class="min-h-screen flex flex-col">
            <header id="app-header" class="w-full border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-[#111111] px-6 py-4"></header>
            <main id="app-content" class="flex-1 px-6 py-8"></main>
        </div>
    </body>
</html>
