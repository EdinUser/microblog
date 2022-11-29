<?php

namespace MicroBlog\Utils;

use MicroBlog\Controllers\DependencyAware;
use Slim\Container;

class Pagination extends DependencyAware
{
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->container = $container;
    }

    /**
     * @var int
     */
    private int $on_page = 10;

    function doPagination(array $arrayToBeSplit = array(), string $baseUrl = '', int $on_page = 10): array
    {
        $baseUrl = $this->container->router->pathFor($baseUrl);
        $currentUrl = $this->container->request->getUri()->getPath();

        if (!empty($on_page)) {
            $this->on_page = $on_page;
        }

        if (preg_match("/.*?\/p(\d*?)$/", $currentUrl, $matches)) {
            $sp = $matches[1];
            $baseUrl = preg_replace("/(.*?)\/p(\d*?)$/", "\\1", $baseUrl);
        } else {
            $sp = 1;
        }

        if (stripos($baseUrl, "/") == 0 && stripos($baseUrl, "/") == 1) {
            $baseUrl = substr($baseUrl, 1);
        }

        $hashAddOn = "";
        if (stripos($baseUrl, "#") !== false) {
            preg_match("/#(\w+)/", $baseUrl, $hashArray);
            if (isset($hashArray[1])) {
                $hashAddOn = "#" . $hashArray[1];
                $baseUrl = preg_replace("/(#\w+)/", "", $baseUrl);
            }
        }

        $allPages = (int)ceil(count($arrayToBeSplit) / $this->on_page);

        if (count($arrayToBeSplit) > $this->on_page) {
            if (!$sp) {
                $sp = 1;
            }
            $pages = $allPages;

            if ($sp > 5) {
                if ($sp > $pages - 5) {
                    $start = $sp - (9 - ($pages - $sp));
                } else {
                    $start = $sp - 4;
                }
                if ($start > 2) {
                    $twigArray['first_page']['url'] = "$baseUrl/p" . ($start - 1) . $hashAddOn;
                }
            } else {
                $start = 1;
            }

            if ($sp < $pages - 5) {
                if ($sp < 5) {
                    $end = 10;
                } else {
                    $end = $sp + 5;
                }
                if ($end < $pages - 1) {
                    $twigArray['last_page']['url'] = "$baseUrl/p" . ($end + 1) . $hashAddOn;
                }
                if ($end > $pages) {
                    $end = $pages;
                }
                $twigArray['last_page']['number'] = $pages;
            } else {
                $end = $pages;
            }

            if ($start < 1) {
                $start = 1;
            }
            for ($i = $start; $i <= $end; $i++) {
                $twigArray['pages'][$i]['url'] = "$baseUrl/p" . $i . $hashAddOn;
                if ($i == $sp) {
                    $twigArray['pages'][$i]['active'] = "active";
                }
            }
        }
        $twigArray['base_url'] = $baseUrl . $hashAddOn;

        $recordsToBeDisplayed = array_chunk($arrayToBeSplit, $on_page, true);

        return array(
          "resultArray" => $recordsToBeDisplayed[$sp - 1] ?? array(),
          "resultTwig"  => $twigArray,
        );
    }

}