<?php
if (!function_exists('customResponse')) {
    function customResponse($data = null, $message = null)
    {
        return new \App\Helpers\ExtendedResponse($data, $message);
    }
}

/**
 * Simple helper to debug to the console
 *
 * @param  Array, String $data
 * @return String
 */

if (!function_exists('debugToConsole')) {
    function debugToConsole($data)
    {
        if (is_array($data)) {
            $output =
                "<script>console.log( 'Debug Objects: " .
                implode(',', $data) .
                "' );</script>";
        } else {
            $output =
                "<script>console.log( 'Debug Objects: " .
                $data .
                "' );</script>";
        }

        echo $output;
    }
}

if (!function_exists('parseNotificationTemplate')) {
    function parseNotificationTemplate($content, $template)
    {
        return preg_replace_callback(
            '/@{(.*?)}/',
            function ($matches) use ($content) {
                list($shortCode, $index) = $matches;

                if (isset($content[$index])) {
                    return $content[$index];
                } else {
                    throw new Exception(
                        "Shortcode $shortCode not found in template id {verify_email",
                        1
                    );
                }
            },
            $template
        );
    }
}
