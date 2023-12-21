<?php
/*
 * Plugin Name: YOUR PLUGIN NAME
 */
namespace CarbonWordPress;

use CarbonPHP\Abstracts\ColorCode;
use CarbonPHP\CarbonPHP;
use CarbonPHP\Interfaces\iColorCode;


class CarbonWordPress
{

    public static bool $verbose = true;

    public static function addCarbonPHPWordpressMenuItem(bool $advanced): void
    {
        $notice = $advanced ? "<Advanced>" : "<Basic>";

        add_action('admin_menu', static fn() => add_menu_page(
            "CarbonPHP $notice",
            "CarbonPHP $notice",
            'edit_posts',
            'CarbonPHP',
            static function () {

                print  <<<HTML
                <div id="root" style="height: 100%;">
                </div>
                <script>
                    window.C6WordPress = true;
                    const manifestURI = 'https://carbonorm.dev/';
                    fetch(manifestURI + 'asset-manifest.json')
                        .then(response => response.json())
                        .then(data => {
                            const entryPoints = data?.entrypoints || [];
                            entryPoints.forEach((value => value.endsWith('.js')
                                ?  jQuery.getScript( manifestURI + value )
                                :  jQuery('<link/>',
                                {
                                    rel: 'stylesheet',
                                    type: 'text/css',
                                    href: manifestURI + value
                                }).appendTo('head')
                            ))
                        });
                </script>
                HTML;

            },
            'dashicons-editor-customchar',
            '4.5'
        ));
    }

    public static function make() : void
    {

        $_ENV['CARBONORM_VERBOSE'] ??= true;

        if ($_ENV['CARBONORM_VERBOSE'] === 'false') {

            self::$verbose = false;

        }

        CarbonPHP::$wordpressPluginEnabled = true;

        if (self::$verbose) {

            ColorCode::colorCode("Starting Full Wordpress CarbonPHP Configuration!",
                iColorCode::BACKGROUND_CYAN);

        }

        (new CarbonPHP(Configuration::class, ABSPATH))(WordpressApplication::class);

        if (false === CarbonPHP::$setupComplete) {

            if (self::$verbose) {

                ColorCode::colorCode("CarbonWordpress detected CarbonPHP had an unexpected finish!", iColorCode::BACKGROUND_RED);

            }

            return ;    // an error occurred

        }

        // todo - licensing!
        self::addCarbonPHPWordpressMenuItem(true);

        if (self::$verbose) {

            ColorCode::colorCode("FINISHED Full Wordpress CarbonPHP Configuration!");

        }

    }

}