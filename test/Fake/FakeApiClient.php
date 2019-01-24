<?php

namespace Test\Aa\AkeneoImport\Fake;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\Pim\ApiClient\Api\AssociationTypeApiInterface;
use Akeneo\Pim\ApiClient\Api\AttributeApiInterface;
use Akeneo\Pim\ApiClient\Api\AttributeGroupApiInterface;
use Akeneo\Pim\ApiClient\Api\AttributeOptionApiInterface;
use Akeneo\Pim\ApiClient\Api\CategoryApiInterface;
use Akeneo\Pim\ApiClient\Api\ChannelApiInterface;
use Akeneo\Pim\ApiClient\Api\CurrencyApiInterface;
use Akeneo\Pim\ApiClient\Api\FamilyApiInterface;
use Akeneo\Pim\ApiClient\Api\FamilyVariantApiInterface;
use Akeneo\Pim\ApiClient\Api\LocaleApiInterface;
use Akeneo\Pim\ApiClient\Api\MeasureFamilyApiInterface;
use Akeneo\Pim\ApiClient\Api\MediaFileApiInterface;
use Akeneo\Pim\ApiClient\Api\ProductApiInterface;
use Akeneo\Pim\ApiClient\Api\ProductModelApiInterface;

class FakeApiClient implements AkeneoPimClientInterface
{
    private $apis = [];

    /**
     */
    public function __construct()
    {
        $this->apis = [
            'product' => new FakeApi(),
            'product_model' => new FakeApi(),
            'product_media_file' => new FakeApi(),
        ];
    }


    public function getRequestLog()
    {
        $log = [];

        foreach ($this->apis as $apiType => $api) {
            foreach ($api->getRequestLog() as $logItem) {
                $log[] = array_merge(['api' => $apiType], $logItem);
            }
        }

        return $log;
    }


    public function getToken() {}

    public function getRefreshToken() {}

    public function getProductApi()
    {
        return $this->apis['product'];
    }

    /**
     * Gets the category API.
     *
     * @return CategoryApiInterface
     */
    public function getCategoryApi()
    {
        // TODO: Implement getCategoryApi() method.
    }

    /**
     * Gets the attribute API.
     *
     * @return AttributeApiInterface
     */
    public function getAttributeApi()
    {
        // TODO: Implement getAttributeApi() method.
    }

    /**
     * Gets the attribute option API.
     *
     * @return AttributeOptionApiInterface
     */
    public function getAttributeOptionApi()
    {
        // TODO: Implement getAttributeOptionApi() method.
    }

    /**
     * Gets the attribute group API.
     *
     * @return AttributeGroupApiInterface
     */
    public function getAttributeGroupApi()
    {
        // TODO: Implement getAttributeGroupApi() method.
    }

    /**
     * Gets the family API.
     *
     * @return FamilyApiInterface
     */
    public function getFamilyApi()
    {
        // TODO: Implement getFamilyApi() method.
    }

    /**
     * Gets the product media file API.
     *
     * @return MediaFileApiInterface
     */
    public function getProductMediaFileApi()
    {
        return $this->apis['product_media_file'];
    }

    /**
     * Gets the locale API.
     *
     * @return LocaleApiInterface
     */
    public function getLocaleApi()
    {
        // TODO: Implement getLocaleApi() method.
    }

    /**
     * Gets the channel API.
     *
     * @return ChannelApiInterface
     */
    public function getChannelApi()
    {
        // TODO: Implement getChannelApi() method.
    }

    /**
     * Gets the currency API.
     *
     * @return CurrencyApiInterface
     */
    public function getCurrencyApi()
    {
        // TODO: Implement getCurrencyApi() method.
    }

    /**
     * Gets the measure family API.
     *
     * @return MeasureFamilyApiInterface
     */
    public function getMeasureFamilyApi()
    {
        // TODO: Implement getMeasureFamilyApi() method.
    }

    /**
     * Gets the association type API.
     *
     * @return AssociationTypeApiInterface
     */
    public function getAssociationTypeApi()
    {
        // TODO: Implement getAssociationTypeApi() method.
    }

    public function getFamilyVariantApi()
    {
        // TODO: Implement getFamilyVariantApi() method.
    }

    public function getProductModelApi()
    {
        return $this->apis['product_model'];
    }
}
