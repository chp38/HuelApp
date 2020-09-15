<?php

namespace App\Repositories\Shopify;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ShopifyRepository
{
    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    private $authToken;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    private $authPassword;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    private $shopifyStore;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    private $prefix;

    /**
     * @var array
     */
    private $lastHeader;

    /**
     * ShopifyRepository constructor.
     */
    public function __construct()
    {
        $this->authToken = config('shopify.auth_token');
        $this->shopifyStore = config('shopify.store');
        $this->authPassword = config('shopify.auth_password');
        $this->prefix = config('shopify.slug_prefix');
    }

    /**
     * Handles the get requests to Shopify.
     *
     * @param string $slug
     * @param array $options
     * @return array
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function get(string $slug, array $options): array
    {
        $parameters = $this->parseParameters($options);

        $url =
            "https://" . $this->authToken . ":" .
            $this->authPassword . "@" .
            $this->shopifyStore .
            $this->prefix . "/" . $slug .
            $parameters
        ;

        $response = Http::get(
            $url
        );

        $response->throw();
        $this->lastHeader = $response->headers();

        return \GuzzleHttp\json_decode($response->body(), true);
    }

    /**
     * @param array $parameters
     * @return string
     */
    private function parseParameters(array $parameters): string
    {
        $parameterString = "?";

        foreach ($parameters as $parameter => $value) {
            $parameterString .= $parameter . "=" . $value . "&";
        }

        return $parameterString;
    }

    /**
     * Get a given header from the last request.
     *
     * @param string $header
     * @return mixed
     * @throws \Exception
     */
    public function getHeader(string $header)
    {
        if (array_key_exists($header, $this->lastHeader)) {
            return $this->lastHeader[$header];
        }

        throw new \Exception('Header' . $header . 'does not exist!');
    }

    /**
     * Get next links from paginated API.
     *
     * @return bool|mixed
     */
    public function getNextLink()
    {
        try {
            $link = $this->getHeader('Link');

            return $this->parseLinkHeader($link);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Extract the links from the Link header. Returns an array of the links
     * available, and the value for the page_info.
     *
     * @param $headerLink
     * @return array
     */
    private function parseLinkHeader($headerLink)
    {
        $availableLinks = [];

        foreach ($headerLink as $link){
            if (preg_match('/<(.*)>;\srel=\\"(.*)\\"/', $link, $matches)) {
                $query = parse_url($matches[1], PHP_URL_QUERY);
                parse_str($query, $params);
                $availableLinks[$matches[2]] = $params['page_info'];
            }
        }

        return $availableLinks;
    }
}