<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>

    <style>
        /* Base layout */
        :root {
            --sidebar-width: 260px;
            --navbar-height: 70px;
            --content-padding: 1.25rem;
            --border-radius: 0.75rem;
            --main-spacing: 1rem;
            --card-height: 165px;
            --icon-size: 48px;
        }

        .content-wrapper {
            min-height: calc(100vh - var(--navbar-height));
            margin: var(--navbar-height) 0 0 var(--sidebar-width);
            margin-left: 10px;
            margin-right: 10px;
            margin-top: 10px;
            padding: var(--main-spacing);
            padding-right: 20px;
            transition: all 0.3s ease;
            position: relative;
            width: calc(100% - var(--sidebar-width));
            display: flex;
            flex-direction: column;
        }

        .content-container {
            flex: 1;
            border-radius: var(--border-radius);
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.08);
            padding: var(--content-padding);
            overflow: auto;
            position: relative;
            height: calc(100vh - var(--navbar-height) - 2 * var(--main-spacing));
        }

       
    </style>

    @yield('styles')
</head>

<body>
    <div class="content-wrapper">
        <div class="content-container">
            {{ $slot }}
        </div>
    </div>

    @yield('scripts')
</body>

</html>
