<?php

namespace OHF\UnifiStats\Util;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use CodeInc\HumanReadableFileSize\HumanReadableFileSize;
use Slim\Views\Twig;

class TwigConfigurationInitializer
{
    public static function create(bool $debug = false, bool $caching = false)
    {
        $timezone = $_ENV['TIMEZONE'] ?? 'UTC';

        $twig = Twig::create('templates', ['cache' => $caching ? 'storage/cache/twig' : false, 'debug' => $debug]);
        if ($debug) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }

        $twig->getEnvironment()->addFilter(new \Twig\TwigFilter('humanInterval', function ($value) {
            return CarbonInterval::seconds($value)->cascade()->forHumans();
        }));

        $twig->getEnvironment()->addFilter(new \Twig\TwigFilter('humanSize', function ($value) {
            return $value !== null ? HumanReadableFileSize::getHumanSize($value, 1) : null;
        }));

        $twig->getEnvironment()->addFilter(new \Twig\TwigFilter('diffForHumans', function ($value) {
            return Carbon::createFromDate($value)->diffForHumans();
        }));

        $twig->getEnvironment()->addFilter(new \Twig\TwigFilter('diffForHumansFromTimestamp', function ($value) {
            return Carbon::createFromTimestamp($value)->diffForHumans();
        }));

        $twig->getEnvironment()->addFilter(new \Twig\TwigFilter('localDateFormat', function ($value) use ($timezone) {
            return Carbon::createFromDate($value)->setTimezone($timezone)->isoFormat('LLL');
        }));

        $twig->getEnvironment()->addFilter(new \Twig\TwigFilter('localDateFormatFromTimestamp', function ($value) use ($timezone) {
            return Carbon::createFromTimestamp($value)->setTimezone($timezone)->isoFormat('LLL');
        }));

        $twig->getEnvironment()->addFilter(new \Twig\TwigFilter('monthYear', function ($value) {
            return Carbon::createFromTimestampMs($value)->format('F Y');
        }));

        $twig->getEnvironment()->addFilter(new \Twig\TwigFilter('unifiDeviceType', function ($value) {
            return match ($value) {
                'uap' => 'Access point',
                'usw' => 'Switch',
                'udm' => 'Dream Machine',
                default => $value,
            };
        }));

        $twig->getEnvironment()->addFilter(new \Twig\TwigFilter('unifiDeviceModel', function ($value) {
            return match ($value) {
                'U7MSH' => 'Access Point AC Mesh', // UAP-AC-M
                'U7NHD' => 'Access Point nanoHD', // UAP-nanoHD
                'U7LR' => 'Access Point AC Long-range', // UAP-AC-LR
                'U7LT' => 'Access Point AC Lite', // UAP-AC-LITE
                'UAL6' => 'Access Point WiFi 6 Lite', // U6-Lite
                'US8P60' => 'Switch 8 PoE (60W)', // US-8-60W
                'USMINI' => 'Switch Flex Mini', // USW-Flex-Mini
                'USL8LP' => 'Switch Lite 8 PoE', // USW-Lite-8-PoE
                'USL16LP' => 'Switch Lite 16 PoE', // USW-Lite-16-PoE
                'USF5P' => 'Switch Flex', // USW-Flex
                'US16P150' => 'Switch 16 PoE', // USW-16-PoE
                'UDMPRO' => 'Dream Machine Pro', // UDM-Pro
                default => $value,
            };
        }));

        $twig->getEnvironment()->addFilter(new \Twig\TwigFilter('unifiNetworkPurpose', function ($value) {
            return match ($value) {
                'wan' => 'WAN',
                'remote-user-vpn' => 'Remote-User VPN',
                'vlan-only' => 'VLAN-only',
                default => ucfirst($value),
            };
        }));

        $twig->getEnvironment()->addFilter(new \Twig\TwigFilter('unifiNetworkType', function ($value) {
            return match ($value) {
                'pppoe' => 'PPPoE',
                'l2tp-server' => 'L2TP Server',
                default => ucfirst($value),
            };
        }));

        $twig->getEnvironment()->addFilter(new \Twig\TwigFilter('unifiWlanSecurityType', function ($value) {
            return match ($value) {
                'wpapsk' => 'WPA-PSK',
                default => ucfirst($value),
            };
        }));

        return $twig;
    }
}