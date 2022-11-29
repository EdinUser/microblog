<?php

namespace MicroBlog\Utils;

use MicroBlog\Controllers\DependencyAware;
use Slim\Container;

class Pagination extends DependencyAware
{

    /**
     * @var int
     */
    private int $on_page = 10;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->container = $container;
    }

    /**
     * Build pagination base on array
     *
     * @param array  $arrayToBeSplit Data to be used and splitted
     * @param string $baseUrl        Named route to be used as a base URL
     * @param int    $on_page        How many items to be on page
     *
     * @return array Array with data for usage (array) and a TWIG data to be send to template
     */
    function doPagination(array $arrayToBeSplit = array(), string $baseUrl = '', int $on_page = 1): array
    {
        $currentUrl = $this->container->request->getUri()->getPath();

        if (!empty($on_page)) {
            $this->on_page = $on_page;
        }

        if (preg_match("/.*?\/p(\d*?)$/", $currentUrl, $matches)) {
            $sp = $matches[1];
        } else {
            $sp = 1;
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
                    $twigArray['first_page']['url'] = $this->container->router->pathFor($baseUrl, ['page' => ($start - 1)]);
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
                    $twigArray['last_page']['url'] = $this->container->router->pathFor($baseUrl, ['page' => ($end + 1)]);
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
                $twigArray['pages'][$i]['url'] = $this->container->router->pathFor($baseUrl, ['page' => $i]);
                if ($i == $sp) {
                    $twigArray['pages'][$i]['active'] = "active";
                }
            }
        }
        $twigArray['base_url'] = $this->container->router->pathFor($baseUrl);

        $recordsToBeDisplayed = array_chunk($arrayToBeSplit, $on_page, true);

        return array(
          "resultArray" => $recordsToBeDisplayed[$sp - 1] ?? array(),
          "resultTwig"  => $twigArray,
        );
    }

}