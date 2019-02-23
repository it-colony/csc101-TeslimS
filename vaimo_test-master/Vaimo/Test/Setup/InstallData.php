<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 12/10/2018
 * Time: 2:07 PM
 */



namespace Vaimo\Test\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\State;
use Psr\Log\LoggerInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var string
     */
    protected $productType = \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    protected $logger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    protected $state;

    public function __construct(
        State $state,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        LoggerInterface $logger
    )
    { $this->state = $state;
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup,
                            ModuleContextInterface $context)
    {
        $productArray = [
            'product 1' => 'A00001',
            'product 3' => 'A00003'
        ];
        $categories = array(2);

        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        foreach ($productArray as $name => $sku) {


            $data = [
                'name' => $name,
                'sku' => $sku,
                'price' => 456,
                'weight' => 1
            ];
            $attributeSetId = 4; //Attribute set default
            $product = $this->productFactory->create();
            $product->setData($data);
            $product->setCategoryIds($categories);
            $product
                ->setTypeId($this->productType)
                ->setAttributeSetId($attributeSetId)
                ->setWebsiteIds([$this->storeManager->getDefaultStoreView()->getWebsiteId()])
                ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                ->setStockData(['is_in_stock' => 1, 'manage_stock' => 0, 'qty' => 1000])
                ->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);

            if (empty($data['visibility'])) {
                $product->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
            }
            try{
                $product->save();

            }catch (\Exception $exception){
                $this->logger->critical($exception->getMessage());
            }
        }
    }
}