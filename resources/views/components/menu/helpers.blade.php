@php
    $currentLocale = LaravelLocalization::getCurrentLocale();
    try {
        $currentRoute = Request::route() ? Request::route()->getName() : null;
    } catch (\Exception $e) {
        $currentRoute = null;
    }
    $currentUrl = Request::url();

    // Helper function to check if menu item is active
    if (!function_exists('isMenuActive')) {
        function isMenuActive($routes, $currentRoute = null, $urlPatterns = [])
        {
            global $currentLocale;

            if ($currentRoute && is_array($routes)) {
                if (in_array($currentRoute, $routes)) {
                    return true;
                }
            } elseif ($currentRoute && is_string($routes)) {
                if ($currentRoute === $routes) {
                    return true;
                }
            }

            // Check URL patterns
            foreach ($urlPatterns as $pattern) {
                if (Request::is($currentLocale . '/' . $pattern)) {
                    return true;
                }
            }

            return false;
        }
    }

    // Helper function to check if parent menu should be open
    if (!function_exists('isParentMenuOpen')) {
        function isParentMenuOpen($childRoutes, $urlPatterns = [])
        {
            global $currentRoute;
            return isMenuActive($childRoutes, $currentRoute, $urlPatterns);
        }
    }

    // Helper function to get badge style based on active state
    if (!function_exists('getBadgeStyle')) {
        function getBadgeStyle($isActive, $color = 'primary')
        {
            $brandingColor =
                $color === 'secondary' ? config('branding.colors.secondary') : config('branding.colors.primary');

            if ($isActive) {
                // Active: white background with branding color text
                return "background-color: white; color: {$brandingColor};";
            } else {
                // Inactive: branding color background with white text
                return "background-color: {$brandingColor}; color: white;";
            }
        }
    }
@endphp
